<?php defined('SYSPATH') or die('No direct script access.');

class Zillow_Transporter
{
    /**
     * Curl resource ID
     * @var string
     */
    private $_conn;
	
    /**
     * Class constructor
     * @access public
     */
    public function __construct ()
    {
        $this->_conn = curl_init();
        curl_setopt( $this->_conn, CURLOPT_RETURNTRANSFER, true );
    }
	
    /**
     * Class destructor
     */
    public function __destruct ()
    {
        curl_close($this->_conn);
    }

    /**
     * Get the contents of the given url as simpleXMLObject. Any errors fetching
     * data will throw an exception.
	 * 
     * @param	string	$uri
     * @param	string	$context - the context for exception handling (valid values: 'search', 'zestimate', 'chart')
     * @return	simpleXMLObject (or void if exception thrown)
     * @throws	Zillow_Exception
     */
    public function get ( $uri ) // context removed for now
    {
        curl_setopt($this->_conn, CURLOPT_URL, $uri);
        $result = curl_exec($this->_conn);
        $http_code = (string) curl_getinfo($this->_conn, CURLINFO_HTTP_CODE);
        $xml = @simplexml_load_string($result);

        if ( $xml === false )
        	throw new Zillow_Exception($http_code.' Error');

        $text = (string) $xml->message->text;
        $code = (string) $xml->message->code;
        
        if ( $code != '0' )
            throw $this->_generate_exception( $code, $text);
        else
            return $xml;
    }
	
	private function _generate_exception ( $code, $text )
	{
		return new Zillow_Exception(':code ' . $text, array(':code'=> $code));
	}
}