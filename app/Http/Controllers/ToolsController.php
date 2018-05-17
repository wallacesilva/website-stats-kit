<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client as HttpClient;

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

        	$data = ['error' => true, 'msg' => 'Could not connect to: '.$url];
            
        }
        
        return response()->json($data);
    }
}
