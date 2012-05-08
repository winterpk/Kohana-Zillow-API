<?php defined('SYSPATH') or die('No direct access allowed.');

abstract class Zillow_Core 
{
	/**
     * Stores a transporter object to inject into created Zillow objects
     * @var	object	$_transporter
     */
	private $_transporter;
	
	/**
     * The zws id string to use throughout
     * @var	string	$_zws_id
     */
    public $zws_id;
	
	
	/**
     * Class Constructor
     */
    public function __construct ()
    {
        $this->zws_id = Kohana::config('zillow')->zws_id;
    }
	
	/**
	 * Factory - method loads a new Zillow object
	 */	
	public static function factory ()
	{
		return new Zillow;
	}	
	
	/**
     * Creates (if not already created) and returns a Zillow_Transporter object
     * @return 	Zillow_Transporter
     */
    public function create_transporter ()
    {
        if ( FALSE === isset($this->_transporter) )
            $this->_transporter = new Zillow_Transporter;

        return $this->_transporter;
    }
	
	/**
     * Finds and returns matching properties with basic elements set.
     * Will throw any number of exceptions based on Zillow error codes
	 * 
     * @param	string	$address
     * @param	string	$csz - city state and/or zip
     * @return	simpleXmlObject	$xml
     * @link	http://www.zillow.com/webservice/GetSearchResults.htm
     * @throws	Zillow_Exception
     */
    public function get_search_results ( $address = NULL, $csz = NULL )
    {
    	$uri = 'http://www.zillow.com/webservice/GetSearchResults.htm';
		$trans = $this->create_transporter();
		$uri .= 
			'?zws-id='.$this->zws_id.
			'&address='.urlencode($address).
			'&citystatezip='.urlencode($csz);
		
		try
		{
			$xml = $trans->get($uri);
			return $xml;
		}
        catch (Zillow_Exception $e)
		{
			throw $e;
		}
	}
	
	/**
     * Gets the Zestimate for a specific zillow property ID (zpid)
	 * 
     * @param	string	$zpid
     * @return	object	xml
     * @link	http://www.zillow.com/webservice/GetZestimate.htm
     * @throws	Zillow_Exception
     */
    public function get_zestimate ( $address = NULL, $csz = NULL )
    {
    	$uri = 'http://www.zillow.com/webservice/GetSearchResults.htm';
		$trans = $this->create_transporter();
		$uri .= 
			'?zws-id='.$this->zws_id.
			'&address='.urlencode($address).
			'&citystatezip='.urlencode($csz);
		
    	try 
    	{
			$xml = $trans->get($uri);
			return $xml;
        } 
        catch (Zillow_Exception $e) 
        {
			throw $e;
        }
	}
	
	/**
     * The GetChart API generates a URL for an image file that displays historical Zestimates for a specific property. 
	 * The API accepts as input the Zillow Property ID as well as a chart type: either percentage or dollar value change. 
	 * Optionally, the API accepts width and height parameters that constrain the size of the image. 
	 * The historical data can be for the past 1 year, 5 years or 10 years.
	 *  
     * @param	string 	$zpid
     * @param	string 	$unit_type - string "dollar" or string "percent"
	 * @param	int		$width - specified image width (not required)
	 * @param	int		$height - specified image height (not required)
	 * @param	string	$duration - specified duration of past data, string "1year", "5years", "10years" (not required)
     * @return 	xml
     * @link	http://www.zillow.com/webservice/GetChart.htm
     * @throws	Zillow_Exception
     */
	public function get_chart ( $zpid = NULL, $unit_type = NULL, $width = NULL, $height = NULL, $duration = NULL )
    {
    	$uri = 'http://www.zillow.com/webservice/GetChart.htm';
		$trans = $this->create_transporter();
		$uri .= 
			'?zws-id='.$this->zws_id.
			'&zpid='.urlencode($zpid).
			'&unit-type='.urlencode($unit_type).
			'&width='.urlencode($width).
			'&height='.urlencode($height).
			'&duration='.urlencode($duration);
			
		try 
		{
			$xml = $trans->get($uri);
			return $xml;
        }
        catch (Zillow_Exception $e) 
        {
            throw $e;
        }
	}
	
	
	/**
     * The GetComps API returns a list of comparable recent sales for a specified property. 
	 * The result set returned contains the address, Zillow property identifier, and Zestimate 
	 * for the comparable properties and the principal property for which the comparables are being retrieved. 
	 * 
     * @param	string 	$zpid  - The Property ID for the property for which to obtain information; the parameter type is an integer
	 * @param	int		$count - The number of comparable recent sales to obtain (integer between 1 and 25)
     * @return	simpleXmlObject $xml
     * @link	http://www.zillow.com/webservice/GetComps.htm
     * @throws	Zillow_Exception
     */
    public function get_comps ( $zpid = NULL, $count = NULL )
    {
    	$uri = 'http://www.zillow.com/webservice/GetComps.htm';
        $trans = $this->create_transporter();
		$uri .= 
			'?zws-id='.$this->zws_id.
			'&zpid='.urlencode($zpid).
			'&count='.urlencode($count);
				
		try 
		{
			$xml = $trans->get($uri);
			return $xml;
        } 
        catch (Zillow_Exception $e) 
        {
            throw $e;
        }
	}
	
	/**
     * For a specified region, the GetDemographics API returns a set of 
	 * market, affordability, real estate, and demographic data.
	 * 
	 * A region can be specified either through its respective Region ID, 
	 * its zipcode, or by providing one to three parameters: state, city, 
	 * neighborhood. The neighborhood parameter can be omitted if only 
	 * city data is desired. 
	 * 
	 * * At least rid or state/city, city/neighborhood, or zip is required
	 * 
     * @param 	string $region_id - The region id of the region to retrieve data from.
     * @param 	string $state - The state of the region to retrieve data from. 
	 * @param 	string $city - The city of the region to retrieve data from. 
     * @param	string $neighborhood - 	The neighborhood of the region to retrieve data from. 
	 * @param	string $zip	- The zipcode of the region to retrieve data from. 
	 * @return 	simpleXmlObject $xml
     * @link 	http://www.zillow.com/webservice/GetDemographics.htm
     * @throws 	Zillow_Exception
     */
    public function get_demographics ( $region_id = NULL, $state = NULL, $city = NULL, $neighborhood = NULL, $zip = NULL )
    {
    	$uri = 'http://www.zillow.com/webservice/GetDemographics.htm';
		$trans = $this->create_transporter();
		$uri .= 
			'?zws-id='.$this->zws_id.
			'&regionid='.urlencode($region_id).
			'&state='.urlencode($state).
			'&city='.urlencode($city).
			'&neighborhood='.urlencode($neighborhood).
			'&zip='.urlencode($zip);
				
		try 
		{
			$xml = $trans->get($uri);
			return $xml;
        } 
        catch (Zillow_Exception $e) 
        {
            throw $e;
        }
	}
	
	/**
     * For a specified region, the GetRegionChildren API returns a list of subregions
	 * 
	 * A region can be specified at various levels of the region hierarchy. An optional
	 * childtype parameter can also be specified to return subregions of a specific type.
	 * 
	 * Allowable region types include: state, county, and city. County is an optional 
	 * parameter unless it is the region to be specified. County and city parameters 
	 * should not be passed together.
	 * 
	 * Possible childtype parameters include: county, city, zipcode, and neighborhood. 
	 * Any childtype parameter can be specified as long as the childtype parameter is 
	 * a subregion type (i.e.. you cannot retrieve the subregion counties of a city).
	 * 
	 * Childtype parameter is optional and defaults to types dependent on the specified 
	 * region type: state defaults to return subregions of type county, county -> city, 
	 * city -> zipcode.
	 * 
     * @param	string	$zpid
     * @return	object	xml
     * @link	http://www.zillow.com/webservice/GetRegionChildren.htm
     * @throws	Zillow_Exception
     */
    public function get_region_children ( $region_id = NULL, $state = NULL, $county = NULL, $city = NULL, $childtype = NULL )
    {
    	$uri = 'http://www.zillow.com/webservice/GetRegionChildren.htm';
		$trans = $this->create_transporter();
		$uri .= 
			'?zws-id='.$this->zws_id.
			'&address='.urlencode($address).
			'&citystatezip='.urlencode($csz);
			
    	try 
    	{
			$xml = $trans->get($uri);
			return $xml;
        } 
        catch (Zillow_Exception $e) 
        {
            throw $e;
        }
	}
	
	/**
     * The GetChart API generates a URL for an image file that displays historical Zestimates for a specific property. 
	 * The API accepts as input the Zillow Property ID as well as a chart type: either percentage or dollar value change. 
	 * Optionally, the API accepts width and height parameters that constrain the size of the image. 
	 * The historical data can be for the past 1 year, 5 years or 10 years.
	 *  
     * @param	string 	$zpid
     * @param	string 	$unit_type - string "dollar" or string "percent"
	 * @param	int		$width - specified image width (not required)
	 * @param	int		$height - specified image height (not required)
	 * @param	string	$duration - specified duration of past data, string "1year", "5years", "10years" (not required)
     * @link	http://www.zillow.com/webservice/GetRegionChart.htm
     * @throws	Zillow_Exception
     */
	public function get_region_chart ( $city=null, $state=null, $neighborhood=null, $zip=null, $unit_type=null, $width=null, $height=null, $duration=null )
    {
    	$uri = 'http://www.zillow.com/webservice/GetRegionChart.htm';
		$trans = $this->create_transporter();
		$uri .=
			'?zws-id='.$this->zws_id.
			'&city='.urlencode($city).
			'&state='.urlencode($state).
			'&neighborhood='.urlencode($neighborhood).
			'&zip='.urlencode($zip).
			'&unit-type='.urlencode($unit_type).
			'&width='.urlencode($width ).
			'&height='.urlencode($height).
			'&chartDuration='.urlencode($duration);
			
		try 
		{
			$xml = $trans->get($uri);
			return $xml;
        } 
        catch (Zillow_Exception $e) 
        {
            throw $e;
        }
	}
	
	/**
     * The GetRateSummary API returns the current rates per loan type — as well as rates 
	 * from a week ago — from Zillow Mortgage Marketplace. Current supported loan types 
	 * are 30-year fixed, 15-year fixed, and 5/1 ARM. Rates are computed from real quotes 
	 * borrowers receive from lenders just seconds before the rate data is returned. 
	 * The GetRateSummary API returns rates for a specific state if the optional state 
	 * parameter is used. 
	 * 
     * @param 	string $state - 	The state for which to return average mortgage rates. 
	 * 								Two-letter state abbreviations should be used 
	 * 								If omitted, national average mortgage rates are returned. 
	 * @param 	string $output -	The type of output desired. Specify 'xml' for XML output 
	 * 								and 'json' for JSON output. If omitted, 'xml' is assumed. 
     * @param	string $callback - 	The name of the JavaScript callback function used to 
	 * 								process the returned JSON data. If specified, the returned 
	 * 								JSON will be wrapped in a function call with the specified 
	 * 								function name. This parameter is intended for use with 
	 * 								dynamic script tags. The callback function is only used 
	 * 								for JSON output.  
	 * @param	string $zip	- The zipcode of the region to retrieve data from. 
	 * @todo 	revisit this one
     * @link 	http://www.zillow.com/webservice/GetRateSummary.htm
     * @throws 	Zillow_Exception
     */
    public function get_rate_summary ( $state = NULL, $output = NULL, $callback = NULL)
    {
    	$uri = 'http://www.zillow.com/webservice/GetRateSummary.htm';
		$trans = $this->create_transporter();
		$uri .= 
			'?zws-id='.$this->zws_id.
			'&regionid='.urlencode($region_id).
			'&state='.urlencode($state).
			'&city='.urlencode($city).
			'&neighborhood='.urlencode($neighborhood).
			'&zip='.urlencode($zip);
			
		try 
		{
			$xml = $trans->get($uri);
			return $xml;
        } 
        catch (Zillow_Exception $e) 
        {
            throw $e;
        }
	}
	
	/**
     * For a specific loan amount, the GetMonthlyPayments API returns the estimated monthly 
	 * payment that includes principal and interest based on today's mortgage rate. The API 
	 * returns the estimated monthly payment per loan type (30-year fixed, 15-year fixed, 
	 * and 5/1 ARM). If a ZIP code is entered, the estimated taxes and insurance are 
	 * returned in the result set. 
	 * 
     * @param	string	$price 	- 		The price of the property for which monthly payment data will be calculated. 
	 * @param	string	$down	-		The percentage of the total property price that will be placed as a 
	 * 									down payment. If omitted, a 20% down payment is assumed. If the down payment 
	 * 									is less than 20%, a monthly private mortgage insurance amount is 
	 * 									specified for each returned loan type. 
	 * @param 	string	$dollarsdown -	The dollar amount that will be placed as a down payment. This amount will 
	 * 									be used for the down payment if the 'down' parameter is omitted. If the down 
	 * 									payment is less than 20% of the purchase price, a monthly private mortgage 
	 * 									insurance amount is specified for each returned loan type.  		
     * @param	string	$zip 	-		The ZIP code in which the property is located. If omitted, monthly property 
	 * 									tax and hazard insurance data will not be returned.
	 * @param	string	$output - 			The type of output desired. Specify 'xml' for XML output and 'json' for JSON 
	 * 									output. If omitted, 'xml' is assumed. 
	 * @param	string	$callback -		The name of the JavaScript callback function used to process the returned 
	 * 									JSON data. If specified, the returned JSON will be wrapped in a function call 
	 * 									with the specified function name. This parameter is intended for use with 
	 * 									dynamic script tags. The callback function is only used for JSON output. 
     * @link	http://www.zillow.com/webservice/GetMonthlyPayments.htm
     * @throws	Zillow_Exception
     */
    public function get_monthly_payments ( $price=null, $down=null, $dollarsdown=null, $zip=null, $output=null, $callback=null )
    {
    	$uri = 'http://www.zillow.com/webservice/GetMonthlyPayments.htm';
		$trans = $this->create_transporter();
		$uri .= 
			'?zws-id='.$this->zws_id.
			'&address='.urlencode($address).
			'&citystatezip='.urlencode($csz);
			
    	try 
    	{
			$xml = $trans->get($uri);
			return $xml;
        } 
        catch (Zillow_Exception $e) 
        {
            throw $e;
        }
	}
	
	/**
     * The GetDeepSearchResults API finds a property for a specified address. 
	 * The result set returned contains the full address(s), zpid and Zestimate 
	 * data that is provided by the GetSearchResults API. Moreover, this API 
	 * call also gives rich property data like lot size, year built, bath/beds, 
	 * last sale details etc. 
	 * 
     * @param 	string $address
     * @param 	string $csz - city state and/or zip
     * @return 	simpleXmlObject $xml
     * @link 	http://www.zillow.com/webservice/GetDeepSearchResults.htm
     * @throws 	Zillow_Exception
     */
    public function get_deep_search_results ( $address=null, $csz=null )
    {
    	$uri = 'http://www.zillow.com/webservice/GetDeepSearchResults.htm';
		$trans = $this->create_transporter();
		$uri .=
			'?zws-id='.$this->zws_id.
			'&address='.urlencode($address).
			'&citystatezip='.urlencode($csz);
			
		try 
		{
			$xml = $trans->get($uri);
			return $xml;
        } 
        catch (Zillow_Exception $e) 
        {
            throw $e;
        }
	}
	
	/**
     * The GetDeepComps API returns a list of comparable recent sales for a specified property. 
	 * The result set returned contains the address, Zillow property identifier, and Zestimate 
	 * for the comparable properties and the principal property for which the comparables are 
	 * being retrieved. This API call also returns rich property data for the comparables. 
	 * 
     * @param	string	$zpid
	 * @param	string	$count
     * @return	object	xml
     * @link 	http://www.zillow.com/webservice/GetDeepComps.htm
     * @throws	Zillow_Exception
     */
    public function get_deep_comps ( $zpid=null, $count=null )
    {
    	$uri = 'http://www.zillow.com/webservice/GetDeepComps.htm';
		$trans = $this->create_transporter();
		$uri .=
			'?zws-id='.$this->zws_id.
			'&zpid='.urlencode($zpid).
			'&count='.urlencode($count);
			
    	try 
    	{
			$xml = $trans->get($uri);
			return $xml;
        } 
        catch (Zillow_Exception $e) 
        {
            throw $e;
        }
	}
	
	/**
     * For a specified property, the GetUpdatedPropertyDetails API returns all of 
	 * the home facts that have been edited by the home's owner or agent. 
     * 
	 * @param	string	$zpid
	 * @return	simplXmlObject	$xml
     * @link	http://www.zillow.com/webservice/GetUpdatedPropertyDetails.htm
     * @throws	Zillow_Exception
     */
	public function get_updated_property_details ( $zpid=null )
    {
    	$uri = 'http://www.zillow.com/webservice/GetUpdatedPropertyDetails.htm';
		$trans = $this->create_transporter();
		$uri .= 
			'?zws-id='.$this->zws_id.
			'&zpid='.urlencode($zpid);
			
		try 
		{
			$xml = $trans->get($uri);
			return $xml;
        } 
        catch (Zillow_Exception $e) 
        {
            throw $e;
        }
	}
}