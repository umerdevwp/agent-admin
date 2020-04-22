<?php

use SmartyStreets\PhpSdk\Exceptions\SmartyException;
use SmartyStreets\PhpSdk\StaticCredentials;
use SmartyStreets\PhpSdk\ClientBuilder;
use SmartyStreets\PhpSdk\US_Street\Lookup;

class Smartystreets_model extends CI_Model
{
    private $sAuthId = null;
    private $sAuthToken = null;

    public function __construct()
    {
        $this->sAuthId = getenv("SMARTYSTREETS_ID");
        $this->sAuthToken = getenv("SMARTYSTREETS_TOKEN");

    }

    public function run() {
        
        $staticCredentials = new StaticCredentials($this->sAuthId, $this->sAuthToken);
        $client = (new ClientBuilder($staticCredentials))
//                        ->viaProxy("http://localhost:8080", "username", "password") // uncomment this line to point to the specified proxy.
                        ->buildUsStreetApiClient();

        // Documentation for input fields can be found at:
        // https://smartystreets.com/docs/cloud/us-street-api

        $lookup = new Lookup();
        //$lookup->setInputId("24601"); // Optional ID from your system
        //$lookup->setAddressee("John Doe");
        $lookup->setStreet("7014 13TH AVE");
        //$lookup->setStreet2("closet under the stairs");
        //$lookup->setSecondary("APT 2");
        $lookup->setUrbanization("");  // Only applies to Puerto Rico addresses
        $lookup->setCity("Brooklyn");
        $lookup->setState("NY");
        $lookup->setZipcode("11229");
        $lookup->setMaxCandidates(3);
        $lookup->setMatchStrategy("invalid"); // "invalid" is the most permissive match,
                                            // this will always return at least one result even if the address is invalid.
                                            // Refer to the documentation for additional MatchStrategy options.

        try {
            $client->sendLookup($lookup);
            $this->displayResults($lookup);
        }
        catch (SmartyException $ex) {
            echo($ex->getMessage());
        }
        catch (\Exception $ex) {
            echo($ex->getMessage());
        }
    }

    public function find($sStreet='',$sCity='',$sState='',$sZipcode='') {


        $staticCredentials = new StaticCredentials($this->sAuthId, $this->sAuthToken);
        $client = (new ClientBuilder($staticCredentials))
//                        ->viaProxy("http://localhost:8080", "username", "password") // uncomment this line to point to the specified proxy.
                        ->buildUsStreetApiClient();

        // Documentation for input fields can be found at:
        // https://smartystreets.com/docs/cloud/us-street-api
//var_dump($sStreet . " - " . $sCity . " " . $sState . " " . $sZipcode );
        $lookup = new Lookup();
        $lookup->setStreet($sStreet);
        $lookup->setCity($sCity);
        $lookup->setState($sState);
        $lookup->setZipcode($sZipcode);
        $lookup->setMaxCandidates(3);
/*
strict: The API will ONLY return candidates that are valid USPS addresses.
range: The API will return candidates that are valid USPS addresses, as well as invalid addresses with primary numbers that fall within a valid range for the street.
invalid: The API will return a single candidate for every properly submitted address, even if invalid or ambiguous.
*/
        $lookup->setMatchStrategy("range");

        try {
            $client->sendLookup($lookup);
//var_dump($response);
            $response = $lookup->getResult();
            if(empty($response))
                $results = ['type'=>'error','results'=>$response];
            else 
                $results = ['type'=>'ok','results'=>$response];

        }
        catch (SmartyException $ex) {
            $results = ['type'=>'error','results'=>$ex->getMessage()];
        }
        catch (\Exception $ex) {
            $results = ['type'=>'error','results'=>$ex->getMessage()];
        }
        //var_dump($results);die;
        return $results;
    }

    public function displayResults(Lookup $lookup) {
        $results = $lookup->getResult();

        if (empty($results)) {
            //echo("\nNo candidates. This means the address is not valid.");
            return false;
        }

        return $results;
        
        $firstCandidate = $results[0];

        echo("\nAddress is valid. (There is at least one candidate)\n");
        echo("\nZIP Code: " . $firstCandidate->getComponents()->getZIPCode());
        echo("\nCounty: " . $firstCandidate->getMetadata()->getCountyName());
        echo("\nLatitude: " . $firstCandidate->getMetadata()->getLatitude());
        echo("\nLongitude: " . $firstCandidate->getMetadata()->getLongitude());
        
        echo "\n";
        echo $firstCandidate->getDeliveryLine1();
        echo "\n";
        echo $firstCandidate->getComponents()->getCityName();
        echo "\n";
        echo $firstCandidate->getComponents()->getStateAbbreviation();
        echo "\n";
        echo $firstCandidate->getComponents()->getZIPCode();
    }

}