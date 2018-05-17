<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as HttpClient;
use League\Uri;

class ToolsController extends Controller
{
    /**
     * Check status to a URL
     * ?url=domain.com - to check url
     * @return \Illuminate\Http\Response - json
     */
    public function checkUrlStatus(Request $request)
    {
        $url = $request->get('url');

        $data = ['error' => true, 'error' => 22, 'msg' => 'Please, fill url to check.'];

        if (is_null($url)) {

            return response()->json($data);

        }

        try {

            $client = new HttpClient();
            $res = $client->request('GET', $url);

            $status_code = $res->getStatusCode();
            
            if ($status_code == 200) {

                $data = ['error' => false, 'error_code' => 0, 'msg' => 'Url is Online'];

            } else {

                $data = ['error' => true, 'error_code' => 22, 'msg' => 'Url is offline and returned status code: '.$status_code];

            }

        } catch (\GuzzleHttp\Exception\ConnectException $e) {

            // dump($e);
            $data = ['error' => true, 'error_code' => $e->getHandlerContext()['errno'], 'msg' => 'Could not connect to: '.$url];

        } catch (\GuzzleHttp\Exception\RequestException $e) {

            // dump($e->getHandlerContext()['errno']);
            if ($e->getHandlerContext()['errno'] == 51) {

                // fail on ssl certificate 
            }
            
            $data = ['error' => true, 'error_code' => $e->getHandlerContext()['errno'], 'msg' => $e->getHandlerContext()['error']];
            
        } catch (Exception $e) {

        	$data = ['error' => true, 'error_code' => 22, 'msg' => 'Could not connect to: '.$url];
            
        }
        
        return response()->json($data);
    }

    public function checkGooglePageSpeed(Request $request)
    {
    	$url = $request->get('url');

    	$data = ['error' => true, 'error' => 22, 'msg' => 'Please, fill url to check.'];

    	if (is_null($url)) {

            return response()->json($data);

        }

        $url_manager = Uri\parse($url); // 'http://foo.com?@bar.com/'

        // dd($url_manager);

        if (is_null($url_manager['scheme'])) {

        	$url_clean = 'http://'.$url_manager['host'];

        }

        $url_clean = $url;

    	$pagespeed_url = sprintf(
    						'https://www.googleapis.com/pagespeedonline/v4/runPagespeed?url=%s&strategy=mobile&key=%s&locale=%s', // 
							$url_clean,
							env('GOOGLE_PAGESPEED_APIKEY'),
							env('GOOGLE_PAGESPEED_LOCALE', 'en') // pt_BR
    					);

    	$client = new HttpClient();
        $res = $client->request('GET', $pagespeed_url);

        //echo $res->getStatusCode();
		// 200
		//echo $res->getHeaderLine('content-type');
		// 'application/json; charset=utf8'
		$content = $res->getBody();

		$data = ['error' => false, 'error_code' => 0, 'msg' => '', 'data' => json_decode($content)];

		return response()->json($data);

		// $response->data->ruleGroups->SPEED->score (int) Boa +80 / Media(60-79) / Ruim (0-59)
		// $response->data->pageStats (array)
    }
}
