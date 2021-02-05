<?php

namespace App\Http\Controllers\Frog;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

class FrogCrawlerController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {

        return view('frog');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function crawl(Request $request)
    {
        $request->validate([
            'urls' => 'required|string|min:7',
        ]);

        $urlList = explode(PHP_EOL, $request->urls);
        $totalPassed = $totalFailed = 0;
        foreach ($urlList as $url) {
            $res = self::curl($url);
            $results['results'][$url]['request_url'] = $url;
            $results['results'][$url]['status_code'] = $res['status_code'];
            $results['results'][$url]['redirect_url'] = $res['redirect_url'];
            $results['results'][$url]['flag'] = ($res['status_code'] == 200) ? 1 : 0;
            if(in_array($res['status_code'],[301,302]) ){
                $res2 = self::curl($res['redirect_url']);
                $results['results'][$url]['rdr']['request_url'] = $res['redirect_url'];
                $results['results'][$url]['rdr']['status_code'] = $res2['status_code'];
                $results['results'][$url]['rdr']['redirect_url'] = $res2['redirect_url'];
                $results['results'][$url]['flag'] = ($res2['status_code'] == 200) ? 1 : 0;
            }

            if ($results['results'][$url]['flag']){
                $totalPassed++;
            }else{
                $totalFailed++;
            }

        }
        $results['summary']= [
            'totalRequest' => count($urlList),
            'totalPassed' => $totalPassed,
            'totalFailed' => $totalFailed,
            ];
            #dd($results);
        return view('frog')->with($results);
    }


    public function googleBot($url) 
    {
        $header = array();
        $header[] = 'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5'; 
        $header[] = 'Cache-Control: max-age=0'; 
        $header[] = 'Content-Type: text/html; charset=utf-8'; 
        $header[] = 'Transfer-Encoding: chunked'; 
        $header[] = 'Connection: keep-alive'; 
        $header[] = 'Keep-Alive: 300'; 
        $header[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7'; 
        $header[] = 'Accept-Language: en-us,en;q=0.5'; 
        $header[] = 'Pragma:'; 
         
        $curl = curl_init();     
        curl_setopt($curl, CURLOPT_URL, $url); 
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header); 
        curl_setopt($curl, CURLOPT_REFERER, 'http://www.google.com'); 
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip, deflate'); 
        curl_setopt($curl, CURLOPT_AUTOREFERER, true); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($curl, CURLOPT_TIMEOUT, 10); 
        $body = curl_exec($curl); 
        curl_close($curl); 
        return $body; 
    } 

    public function fgc($url) 
    {
        return json_decode(file_get_contents( trim($url),true));

    }
    public function curl($url) 
    {
        $curl = curl_init();     
        curl_setopt($curl, CURLOPT_URL, $url); 
        curl_setopt($curl, CURLOPT_NOBODY, false);       
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        $body = curl_exec($curl); 
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
        $redirect_url = curl_getinfo($curl, CURLINFO_REDIRECT_URL); 

        $result = [
            'status_code' => $status_code,
            'redirect_url' => $redirect_url,
        ];

        curl_close($curl); 
        return $result; 
    }


}
