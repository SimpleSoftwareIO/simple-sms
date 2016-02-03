<?php namespace SimpleSoftwareIO\SMS\Drivers;
/**
 * Created by PhpStorm.
 * User: shatilov
 * Date: 02.02.16
 * Time: 10:35
 */
use SimpleSoftwareIO\SMS\IncomingMessage;
use SimpleSoftwareIO\SMS\OutgoingMessage;

class SmscSmppSMS extends AbstractSMS implements DriverInterface
{
    protected $smsc_host;
    protected $smsc_port;
    protected $smsc_login;
    protected $smsc_password;
    protected $smsc_charset;
    protected $smsc_use_ssl;

    private $socket;
    private $sequence_number = 1;

    public function __construct(array $config = [])
    {
        $this->smsc_host        = $config['SMSC_HOST'];
        $this->smsc_port        = $config['SMSC_PORT'];
        $this->smsc_ssl_port    = $config['SMSC_SSL_PORT'];
        $this->smsc_login       = $config['SMSC_LOGIN'];
        $this->smsc_password    = $config['SMSC_PASSWORD'];
        $this->smsc_charset     = $config['SMSC_CHARSET'];
        $this->smsc_use_ssl     = $config['SMSC_USE_SSL']?:false;

        $ip = gethostbyname($this->smsc_host);

        if ($ip == $this->smsc_host) // dns fail
            $ip = "212.24.33.196"; // fixed ip

        $protocol = $this->smsc_use_ssl ? 'ssl://' : '';
        $port = $this->smsc_use_ssl ? $this->smsc_ssl_port : $this->smsc_port;

        if($this->smsc_use_ssl)
        {
            $context = stream_context_create();
            stream_context_set_option($context, 'ssl', 'verify_host', false);
            stream_context_set_option($context, 'ssl', 'verify_peer', false);
            stream_context_set_option($context, 'ssl', 'verify_peer_name', false);
            stream_context_set_option($context, 'ssl', 'allow_self_signed', true);
        }
        else
        {
            $context = null;
        }

        $this->socket = stream_socket_client($protocol.$ip.":".$port, $errno, $errstr, 30 , STREAM_CLIENT_CONNECT , $context);

        if (!$this->socket) {
            die("Не могу соединиться: $errstr ($errno)");
        }

        if (!$this->bind())
            throw new \RuntimeException('Socket Error');
    }

    public function send(OutgoingMessage $message)
    {
        $composeMessage = $message->composeMessage();
        $sender = $message->getFrom()?:".";

        foreach ($message->getTo() as $to) {
            $this->send_sms($to , $composeMessage , $message->getFrom() , $sender);
        }
    }

    protected function processReceive($raw)
    {
        throw new \RuntimeException('SmscSMPP does not support Inbound API Calls.');
    }

    public function checkMessages(array $options = [])
    {
        throw new \RuntimeException('SmscSMPP does not support Inbound API Calls.');
    }

    public function getMessage($messageId)
    {
        throw new \RuntimeException('SmscSMPP does not support Inbound API Calls.');
    }

    public function receive($raw)
    {
        throw new \RuntimeException('SmscSMPP does not support Inbound API Calls.');
    }

    public function __destruct()
    {
        if ($this->socket) {
            $this->unbind();
            fclose($this->socket);
        }
    }

    private function send_pdu($pdu)
    {
        $length = strlen($pdu);

        if(fwrite($this->socket, $pdu, $length) == $length);
        {
            $reply = unpack("Nlen/Ncmd_id/Nstatus/Nseq/a*data", $this->read_pdu());
            if ($reply['seq'] == $this->sequence_number++ && $reply['status'] == 0) // ok
                return $reply['data'];
        }
        return false;
    }

    private function read_pdu()
    {
        $pdu = fread($this->socket , 16);
        $header = unpack("N4", $pdu);
        if($header[1] - 16 > 0) $pdu .= fread($this->socket, $header[1] - 16); // body
        return $pdu;
    }

    private function bind($system_type = '')
    {
        $pdu = pack("a".strlen($this->smsc_login)."xa".strlen($this->smsc_password)."xa".strlen($system_type)."xCCCx", $this->smsc_login, $this->smsc_password, $system_type, 0x34, 5, 1); // body
        $pdu = pack("NNNN", strlen($pdu) + 16, 0x02/*BIND_TRANSMITTER*/, 0, $this->sequence_number).$pdu; // header + body

        return $this->send_pdu($pdu);
    }

    public function unbind()
    {
        $pdu = pack("NNNN", 16, 0x06/*UNBIND*/, 0, $this->sequence_number);
        $this->send_pdu($pdu);
    }

    // Функция отправки SMS
    //
    // обязательные параметры:
    //
    // $phones - список телефонов через запятую или точку с запятой
    // $message - отправляемое сообщение
    //
    // необязательные параметры:
    //
    // $sender - имя отправителя (Sender ID). Для отключения Sender ID по умолчанию необходимо в качестве имени
    // передать пустую строку или точку.

    public function send_sms($phone, $message, $sender = ".", $valid = "") // $message в кодировке SMSC_CHARSET
    {
        if (preg_match('/[`\x80-\xff]/', $message)) { // is UCS chars
            $message = iconv($this->smsc_charset, "UTF-16BE", $message);
            $coding = 2; // UCS2
        }
        else
            $coding = 1; // 8bit

        $sm_length = strlen($message);

        if ($valid) {
            $valid = min((int)$valid, 24 * 60);
            $valid = sprintf('0000%02d%02d%02d00000R', (int)($valid / 1440), ($valid % 1440) / 60, $valid % 60);
        }

        $pdu = pack("xCCa".strlen($sender)."xCCa".strlen($phone)."xCCCa1a".strlen($valid)."xCCCCCnna".$sm_length, // body
            5,			// source_addr_ton
            1,			// source_addr_npi
            $sender,	// source_addr
            1,			// dest_addr_ton
            1,			// dest_addr_npi
            $phone,		// destination_addr
            0,			// esm_class
            0,			// protocol_id
            3,			// priority_flag
            "",			// schedule_delivery_time
            $valid,		// validity_period
            0,			// registered_delivery_flag
            0,			// replace_if_present_flag
            $coding * 4,// data_coding
            0,			// sm_default_msg_id
            0,			// sm_length + short_message [empty]
            0x0424,		// TLV message_payload tag
            $sm_length, // message length
            $message	// message
        );

        $pdu = pack("NNNN", strlen($pdu) + 16, 0x04/*SUBMIT_SM*/, 0, $this->sequence_number).$pdu; // header + body

        return $this->send_pdu($pdu); // message id or false on error
    }
}