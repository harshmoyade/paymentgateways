<?php namespace Sunil\Payments\Gateways;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Sunil\Payments\Exceptions\IndipayParametersMissingException;

class CitrusGateway implements PaymentGatewayInterface {

    protected $parameters = array();
    protected $testMode = false;
    protected $hash = '';
    protected $vanityUrl = '';
    protected $liveEndPoint = 'https://www.citruspay.com/';
    protected $testEndPoint = 'https://checkout.citruspay.com/ssl/checkout/';
    public $response = '';

    function __construct()
    {
        $this->vanityUrl = Config::get('ggpay.citrus.vanityUrl');
        $this->secretKey = Config::get('ggpay.citrus.secretKey');
        $this->testMode = Config::get('ggpay.testMode');

        $this->parameters['merchantTxnId'] = $this->generateTransactionID();
        $this->parameters['currency'] = 'INR';
        $this->parameters['returnUrl'] = url(Config::get('ggpay.citrus.returnUrl'));
        $this->parameters['notifyUrl'] = url(Config::get('ggpay.citrus.notifyUrl'));
    }

    public function getEndPoint()
    {
        return $this->testMode?$this->testEndPoint.$this->vanityUrl:$this->liveEndPoint.$this->vanityUrl;
    }

    public function request($parameters)
    {
        $this->parameters = array_merge($this->parameters,$parameters);
        if(isset($parameters['amount']))
            $this->parameters['orderAmount'] = $parameters['amount'];
        if(isset($parameters['firstname']))
            $this->parameters['firstName'] = $parameters['firstname'];

        $this->checkParameters($this->parameters);

        $this->encrypt();

        return $this;

    }

    /**
     * @return mixed
     */
    public function send()
    {
        Log::info('Indipay Payment Request Initiated: ');

        return View::make('sunil::citrus')->with('hash',$this->hash)
                             ->with('parameters',$this->parameters)
                             ->with('endPoint',$this->getEndPoint());

    }


    /**
     * Check Response
     * @param $request
     * @return array
     */
    public function response($request)
    {
        $response = $request->all();

        $response_hash = $this->decrypt($response);

        if($response_hash!=$response['signature']){
            return 'Signature Mismatch';
        }

        return $response;
    }


    /**
     * @param $parameters
     * @throws IndipayParametersMissingException
     */
    public function checkParameters($parameters)
    {

        $validator = Validator::make($parameters, [
            'merchantTxnId' => 'required',
            'currency' => 'required',
            'returnUrl' => 'required|url',
            'orderAmount' => 'required|numeric',
        ]);

        if ($validator->fails()) {

            throw new IndipayParametersMissingException;
        }

    }

    /**
     * Citrus Encrypt Function
     *
     */
    protected function encrypt()
    {

        $hash_string = $this->vanityUrl.$this->parameters['orderAmount'].$this->parameters['merchantTxnId'].$this->parameters['currency'];

        $this->hash = hash_hmac('sha1', $hash_string, $this->secretKey);
//        dd($this->hash,$this->vanityUrl,$this->parameters['orderAmount'],$this->parameters['merchantTxnId'],$this->parameters['currency'],$this->secretKey);
    }

    /**
     * Citrus Decrypt Function
     *
     * @param $response
     * @return string
     */
    protected function decrypt($response)
    {
        $hash_string = '';
        $hash_string .= $response['TxId'];
        $hash_string .= $response['TxStatus'];
        $hash_string .= $response['amount'];
        $hash_string .= $response['pgTxnNo'];
        $hash_string .= $response['issuerRefNo'];
        $hash_string .= $response['authIdCode'];
        if(isset($response['firstName']))
            $hash_string .= $response['firstName'];
        if(isset($response['lastName']))
            $hash_string .= $response['lastName'];
        $hash_string .= $response['pgRespCode'];
        if(isset($response['addressZip']))
            $hash_string .= $response['addressZip'];


        return hash_hmac('sha1', $hash_string, $this->secretKey);

    }



    public function generateTransactionID()
    {
//        return uniqid();
        return substr(hash('sha256', mt_rand() . microtime()), 0, 20);
    }


    public function fieldNameChangeByGateWay($data){

    }


}