<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);

class rec_driver{
    public $site;
    function __construct($url){
        if(strpos($url ,  "rokna") !== false){
            $this->site = "rokna";
        }elseif(strpos($url ,  "eghtesadeirani") !== false){
            $this->site = "eghtesadeirani";
        }elseif(strpos($url ,  "khatesalamat") !== false){
            $this->site = "khatesalamat";   
        }else{
            $this->site = false;
        }
    }
}

// Class
class grab_data_rk{
    public $url;
    public $short_url;
    public $site_name;
    public $title;
    public $desc;
    public $img;
    public $icon;

    function __construct($url){
        if (filter_var($this->url_enc($url), FILTER_VALIDATE_URL) == FALSE){
            die("The URL is Invalid.");
        }else{
            $url = $this->url_enc($url);
            $this->url = $url;
        }

        // if(strpos($url , "rokna.net")){
        //     $this->site_name = "rokna";
        // }else{
        //     die("this is not a Valid Rokna URL");
        // }

        // CURL to Get Data
        $data = $this->curl($url);

        // Processing Data
        $this->grabber($data);
    }

    public function url_enc($url){
        $url = urldecode($url);
    
        if(stripos($url , "https://") !== null || 
           stripos($url , "http://") !== null){
            $slash_pos = stripos($url , "/" , 9);
            if($slash_pos){
                $url_route = substr($url , $slash_pos+1);
                $domain = substr($url , 0 , $slash_pos + 1);
                $url = $domain . urlencode($url_route);
            }
        }else{
            $slash_pos = stripos($url , "/" , 2);
            if($slash_pos){
                $url_route = substr($url , $slash_pos);
                $domain = substr($url , 0 , $slash_pos + 1);
                $url = $domain . urlencode($url_route);
            }
        }
    
        return $url;
    }

    public function set_url($url){
        $url = $this->url_enc($url);
        $this->url = $url;
    }

    public function get_url(){
        return $this->url;
    }

    public function curl($url){
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:106.0) Gecko/20100101 Firefox/106.0',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive',
            'Cookie: _pk_id.2.482c=467a5718553c4707.1666010982.; zcx_ir_0_4c19_su=AAAAAhQDBgARIDQwZTFjODE0NTk2YWFkMzA2NjczZGM0ZDg5N2Q0NTFkBgERIDQzNWZlMDdhZTFjYTFhM2U0NWNiOWM1MTg2YmM5MzAzBgIRIDdiMThmOWU1MmZmOTQ5NTkxYzQ0NGU2Yzk0Y2U5NGQ4; zcx_ir_0_4c19_c[u]=SsVOBy; ACNNOCACHE=ON; _ga_FQ74GE5YHY=GS1.1.1666434595.2.1.1666435739.60.0.0; _ga=GA1.2.300840273.1666040804; _ga_311ESJMFSB=GS1.1.1666434595.2.1.1666435739.60.0.0; _ym_uid=1666040807469475845; _ym_d=1666040807; zcx_ir_0_4c19_c[uc]=94; MEDIAAD_USER_ID=5ba8a4f1-6a20-4665-a2be-a53e28b01051; zcx_ir_0_4c19_sid=q3onq4hcgc0tum812thtep3373; _5e47eb4b310a2a511e1d1862=true; _gid=GA1.2.1536904204.1666434600; _ym_visorc=b; _ym_isad=2; _pk_ses.2.482c=1; zcx_ir_0_4c19_remember=9-G7E6mV_c9FviHVfARZiQWjExhdz3bkSihDzOiyI_vZL89eIfFYe-X_1H591Q4pL2qqgqLpuytisKuaAVp5mZbEIsntf-5MGrdYlFPI1W4idp7V7CB1icNkjY2rVRFj; _gat_gtag_UA_106912304_1=1; zcx_ir_0_4c19_c[u]=CrTjSr; zcx_ir_0_4c19_su=AAAAAhQDBgARIDBjYzUzYjRiOTIzOGQ4OWNhYWI2NGIyNzBlYjJkM2E0BgERIGQ5YzVlZWJjNzI4NDgwNjE0MzdjMmIxNWQxODczOWE1BgIRIDE0NGFkYzVlNTg4MGJkNmI2MDk0YjI5ZWY1ODM5YWI4',
            'Upgrade-Insecure-Requests: 1',
            'Sec-Fetch-Dest: document',
            'Sec-Fetch-Mode: navigate',
            'Sec-Fetch-Site: none',
            'Sec-Fetch-User: ?1',
            'Pragma: no-cache',
            'Cache-Control: no-cache',
            'TE: trailers'
        ),
        ));
        $response_url = curl_exec($curl);
        $response_url_httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $response_url_curl_info = curl_getinfo($curl);

        // Edit Needed
        if($curl_error = curl_error($curl)){
            $req_count = 0; 
            do{
                $response_url = curl_exec($curl);
                $req_count++;
            }while( !curl_error($curl) || $req_count < 2);
        }

        if($curl_error = curl_error($curl)){
            echo "Application Can't Retrive Data from URL: <br>";
            if($url){
                echo "the Req URL: " . $url . "<br>";
            }
            echo "<pre>";
            echo "<br> -Curl Error-: <br>";
            var_dump($curl_error);
            echo "<br> -Curl Info-: <br>";
            var_dump($curl_info);
            echo "</pre>";
            die();
        }
        if($response_url_httpcode == "404"){
            die("404 The Page Not Found");
        }
        curl_close($curl);
        return $response_url;
    }

    public function grabber($data){
        $dom = new DomDocument;
        @$dom->loadHTML($data);
        $xpath = new DomXPath($dom);

        // Getting Title
        $title = $xpath->query("//meta[@property='og:title']");
        $title = trim($title[0]->getAttribute("content"));
        if(mb_strlen($title) > 75){
            $title = mb_substr($title , 0 , 72);
            $title = $title."...";
        }
        $this->title = $title;

        // Getting Lead/Description
        $lead = $xpath->query("//meta[@property='og:description']");
        $lead = trim($lead[0]->getAttribute("content"));
        if(mb_strlen($lead) > 50){
            $lead = mb_substr($lead , 0 , 47);
            $lead = $lead . "...";
        }
        $this->desc = $lead;

        if(strpos($this->desc , "به گزارش حوادث رکنا:") !== false){
            $this->desc = str_replace("به گزارش حوادث رکنا:" , "" , $this->desc);
        }elseif(strpos($this->desc , "حوادث رکنا:") !== false){
            $this->desc = str_replace("حوادث رکنا:" , "" , $this->desc);
        }elseif(strpos($this->desc , "به گزارش رکنا:") !== false){
            $this->desc = str_replace("به گزارش رکنا:" , "" , $this->desc);
        }

        // Getting img URL
        $img_url = $xpath->query("//meta[@property='og:image']");
        if($img_url->length != 0){
            $img_url = trim($img_url[0]->getAttribute("content"));
            // $img_url = str_replace("cdn" , "static0" , $img_url);
        }else{
            $img_url = "default/default_media.jpg";
        }
        $this->img = $img_url;

        // Getting Short URL
        $short_url = $xpath->query('//main')[0]->getAttribute("data-entity-id");
        $this->short_url = "https://www.rokna.net/fa/tiny/news-".$short_url;
    }

    public function crop_img($main_image , $final_image_name , $final_width , $final_height){
        if(!isset($main_image)){
            return "give a img";
        }
        if(!isset($final_width)){
            return "give a width";
        }
        if(!isset($final_height)){
            return "give a height";
        }
    
        copy ($main_image, 'temp'.DIRECTORY_SEPARATOR.'post_image.jpg');
        $org_image = imagecreatefromjpeg('temp'.DIRECTORY_SEPARATOR.'post_image.jpg');
    
        $width = imagesx($org_image);
        $height = imagesy($org_image);
    
        $orginal_aspect = $width / $height;
        $final_aspect = $final_width / $final_height;
    
        if($orginal_aspect >= $final_aspect){
            $new_height = $final_height;
            // $new_width = $width / ($height / $final_height);
            $new_width = intval($width / ($height / $final_height));
        }else{
            $new_width = $final_width;
            // $new_height = $height / ($width / $final_width);
            $new_height = intval($height / ($width / $final_width));
        }
    
        $final_image = imagecreatetruecolor($final_width , $final_height);
    
        @imagecopyresampled(
            $final_image,
            $org_image,
            0 - ($new_width - $final_width) / 2,
            0 - ($new_height - $final_height) / 2,
            0,
            0,
            $new_width, $new_height,
            $width, $height
        );
        if(imagejpeg($final_image, $final_image_name, 80)){
            return true;
        }
    }
}

class grab_data_cb extends grab_data_rk{
    public function grabber($data){
        $dom = new DomDocument;
        @$dom->loadHTML($data);
        $xpath = new DomXPath($dom);

        // Getting Title
        $title = $xpath->query("//h1[@class='jeg_post_title']");
        $title = trim($title[0]->nodeValue);
        if(mb_strlen($title) > 75){
            $title = mb_substr($title , 0 , 72);
            $title = $title."...";
        }
        $this->title = $title;

        // Getting Lead/Description
        $lead = $xpath->query('//div[@class="content-inner "]//p');
        if($lead[0]->firstChild->tagName == "video"){
            $lead = trim($lead[1]->nodeValue);
        }else{
            $lead = trim($lead[0]->nodeValue);
        }
        if(mb_strlen($lead) > 150){
            $lead = mb_substr($lead , 0 , 147);
            $lead = $lead . "...";
        }
        $this->desc = $lead;

        // Getting Post Image URL
        // Post Image URL
        if($xpath->query('//div[@class="jeg_featured featured_image "]/a/div/img/@src')->length){
            $img_url = $xpath->query('//div[@class="jeg_featured featured_image "]/a/div/img/@src');
            $img_url = trim($img_url[0]->nodeValue);
            $img_url = str_replace("https" , "http" , $img_url);
        }elseif($xpath->query('//div[@class="jeg_featured featured_image"]/a/div/img/@src')->length){
            $img_url = $xpath->query('//div[@class="jeg_featured featured_image"]/a/div/img/@src');
            $img_url = trim($img_url[0]->nodeValue);
            $img_url = str_replace("https" , "http" , $img_url);
        }else{
            $img_url = "default/default_media.jpg";
        }
        $this->img = $img_url;
    }
    public function curl($url){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => '0',
            CURLOPT_HTTPHEADER => array(
              'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:109.0) Gecko/20100101 Firefox/109.0',
              'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
              'Accept-Language: en-US,en;q=0.5',
              'Accept-Encoding: gzip, deflate, br',
              'Referer: https://cinemabartar.ir/',
              'Connection: keep-alive',
              'Cookie: _ga_W0KLN0JD8J=GS1.1.1674530814.37.1.1674531238.0.0.0; _ga=GA1.2.1984102767.1666704612; wp-settings-1=libraryContent%3Dbrowse%26mfold%3Do%26editor%3Dtinymce%26hidetb%3D1%26imgsize%3Dfull%26align%3Dcenter%26editor_plain_text_paste_warning%3D2%26post_dfw%3Doff%26posts_list_mode%3Dlist%26widgets_access%3Doff; wp-settings-time-1=1666970739; _ga_J14CNSMLR5=GS1.1.1674530814.26.1.1674531238.0.0.0; wordpress_logged_in_30b3e3f9619ac09ae73b6f1ed610eca9=admin%7C1675172937%7CSqfBO1D9Uf6JABV9DZQi7Cogetr27Z63DMSOXE1vGCB%7C9e369e9564741f9f1708a79f3466d2142bf0e3c5afddda99b97ab2aa848b2e28; _gid=GA1.2.1281192816.1674482311; jnews_view_counter_visits[0]=1674534840b40243',
              'Upgrade-Insecure-Requests: 1',
              'Sec-Fetch-Dest: document',
              'Sec-Fetch-Mode: navigate',
              'Sec-Fetch-Site: same-origin',
              'Pragma: no-cache',
              'Cache-Control: no-cache'
            ),
        ));
        $response_url = curl_exec($curl);
        $response_url_httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $response_url_curl_info = curl_getinfo($curl);

        // Edit Needed
        if($curl_error = curl_error($curl)){
            $req_count = 0; 
            do{
                $response_url = curl_exec($curl);
                $req_count++;
            }while( !curl_error($curl) || $req_count < 2);
        }

        if($curl_error = curl_error($curl)){
            echo "Application Can't Retrive Data from URL: <br>";
            if($url){
                echo "the Req URL: " . $url . "<br>";
            }
            echo "<pre>";
            echo "<br> -Curl Error-: <br>";
            var_dump($curl_error);
            echo "<br> -Curl Info-: <br>";
            var_dump($curl_info);
            echo "</pre>";
            die();
        }
        // if($response_url_httpcode == "404"){
        //     echo urldecode($url);
        //     echo "<br>";
        //     die("404 The Page Not Found");
        // }
        curl_close($curl);
        return $response_url;
    }
}

class grab_data_kht extends grab_data_rk{
    public function grabber($data){
        $dom = new DomDocument;
        @$dom->loadHTML($data);
        $xpath = new DomXPath($dom);

        // Getting Title
        $title = $xpath->query("//meta[@property='og:title']");
        $title = trim($title[0]->getAttribute("content"));
        if(mb_strlen($title) > 75){
            $title = mb_substr($title , 0 , 72);
            $title = $title."...";
        }
        $this->title = $title;

        // Getting Lead/Description
        $lead = $xpath->query("//meta[@property='og:description']");
        $lead = trim($lead[0]->getAttribute("content"));
        if(mb_strlen($lead) > 50){
            $lead = mb_substr($lead , 0 , 47);
            $lead = $lead . "...";
        }
        $this->desc = $lead;

        // Getting img URL
        $img_url = $xpath->query("//meta[@property='og:image']");
        if($img_url->length != 0){
            $img_url = trim($img_url[0]->getAttribute("content"));
            // $img_url = str_replace("cdn" , "static0" , $img_url);
        }else{
            $img_url = "default/default_media.jpg";
        }
        $this->img = $img_url;

        // Getting Short URL
        $short_url = $xpath->query('//main')[0]->getAttribute("data-entity-id");
        $this->short_url = "https://www.rokna.net/fa/tiny/news-".$short_url;
    }
    public function curl($url){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:109.0) Gecko/20100101 Firefox/109.0',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.5',
                // 'Accept-Encoding: gzip, deflate, br',
                'Referer: https://khatesalamat.ir/',
                'Alt-Used: khatesalamat.ir',
                'Connection: keep-alive',
                'Cookie: _ga_8S8W234JL5=GS1.1.1674608682.23.1.1674608920.0.0.0; _ga=GA1.2.1552474037.1668424527; wp-settings-1=editor%3Dtinymce%26libraryContent%3Dbrowse%26posts_list_mode%3Dlist%26hidetb%3D1%26editor_plain_text_paste_warning%3D2%26imgsize%3Dfull; wp-settings-time-1=1668439322; _ga=GA1.1.1552474037.1668424527; wordpress_logged_in_688465840cfec5370fb51046afc4b139=%40dmin%7C1675558460%7CYZEDxzAauqw9tgAffJNqpWvmoSu1LJpiUsV05OEQLP7%7C364d2e8bcfc7893d07137539d1e132905b4e038d37bd05dfdd98945fbbb2507c; wfwaf-authcookie-91710f84027cb9061a230ee9b08200ad=1%7Cadministrator%7Cmanage_options%2Cunfiltered_html%2Cedit_others_posts%2Cupload_files%2Cpublish_posts%2Cedit_posts%2Cread%7C68c2354c09ef57452def49598565b0c708913762088c1d89b51c635f6c3e8046; _gid=GA1.2.132766055.1674608685; MEDIAAD_USER_ID=62d055dd-9e34-4289-ac0a-97c299a13a70',
                'Upgrade-Insecure-Requests: 1',
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: same-origin',
                'Pragma: no-cache',
                'Cache-Control: no-cache'
            ),
        ));
        $response_url = curl_exec($curl);
        $response_url_httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $response_url_curl_info = curl_getinfo($curl);

        // Edit Needed
        if($curl_error = curl_error($curl)){
            $req_count = 0; 
            do{
                $response_url = curl_exec($curl);
                $req_count++;
            }while( !curl_error($curl) || $req_count < 2);
        }

        if($curl_error = curl_error($curl)){
            echo "Application Can't Retrive Data from URL: <br>";
            if($url){
                echo "the Req URL: " . $url . "<br>";
            }
            echo "<pre>";
            echo "<br> -Curl Error-: <br>";
            var_dump($curl_error);
            echo "<br> -Curl Info-: <br>";
            var_dump($curl_info);
            echo "</pre>";
            die();
        }
        // if($response_url_httpcode == "404"){
        //     echo urldecode($url);
        //     echo "<br>";
        //     die("404 The Page Not Found");
        // }
        curl_close($curl);
        return $response_url;
    }

    public function khat_url($url){
        if(strrpos($url , "/")+1 == strlen($url)){
            $aurl = substr($url , 0 , -1);
        }else{
            $aurl = $url;
        }
        $slash = strrpos($aurl , "/");
        $enc = substr($aurl , $slash + 1);
        $furl = substr($aurl , 0 , $slash + 1);
        $new_url = $furl . urlencode($enc);
    
        return $new_url;
    }
}

class grab_data_eght extends grab_data_rk{
    public function grabber($data){
        $dom = new DomDocument;
        @$dom->loadHTML($data);
        $xpath = new DomXPath($dom);

        // Getting Title
        $title = $xpath->query("//meta[@property='og:title']");
        $title = trim($title[0]->getAttribute("content"));
        if(mb_strlen($title) > 75){
            $title = mb_substr($title , 0 , 72);
            $title = $title."...";
        }
        $this->title = $title;

        // Getting Lead/Description
        $lead = $xpath->query("//meta[@property='og:description']");
        $lead = trim($lead[0]->getAttribute("content"));
        if(mb_strlen($lead) > 50){
            $lead = mb_substr($lead , 0 , 47);
            $lead = $lead . "...";
        }
        $this->desc = $lead;

        if(strpos($this->desc , "اقتصاد ایرانی ؛") !== false){
            $this->desc = str_replace("اقتصاد ایرانی ؛" , "" , $this->desc);
        }elseif(strpos($this->desc , "اقتصاد ایرانی") !== false){
            $this->desc = str_replace("اقتصاد ایرانی" , "" , $this->desc);
        }

        // Getting img URL
        $img_url = $xpath->query("//meta[@property='og:image']");
        if($img_url->length != 0){
            $img_url = trim($img_url[0]->getAttribute("content"));
        }else{
            $img_url = "default/default_media.jpg";
        }
        $this->img = $img_url;

        // Getting Short URL
        $short_url = $xpath->query('//main')[0]->getAttribute("data-entity-id");
        $this->short_url = "https://www.eghtesadeirani.ir/fa/tiny/news-".$short_url;
    }
}

class push_to_snjgh{
    public $snj_url = "https://back.sanjagh.com/api/panel/ads";
    const RK_CAMP_ID = "5ef302f806aa0d0221085583";
    const CB_CAMP_ID = "6187c024f87e6e71802c1382";
    const SHK_CAMP_ID = "6186471af9916649360fbc42";
    public $boundary;
    public $delimiter;
    public $headers = [
        "Host: back.sanjagh.com",
        "Accept: application/json, text/plain, */*",
        "Accept-Language: en-US,en;q=0.5",
        // "Accept-Encoding: gzip, deflate, br",
        'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9iYWNrLnNhbmphZ2guY29tXC9hcGlcL3BhbmVsXC9sb2dpbiIsImlhdCI6MTY2NjEyMzgwOCwiZXhwIjoxNjk3NjU5ODA4LCJuYmYiOjE2NjYxMjM4MDgsImp0aSI6IjBrOVFOYzV4bDZGTmpYZGUiLCJzdWIiOjksInBydiI6Ijk0YzFhODViYTMyOTgyNTk2MGQ2MTQyOGM1Zjc0YzM4YmY2ZjBmMzIifQ.QuWXbT3LI7v1Y8U6SIdo3udWng2WrckpG0eGpdopw4A',
        "Origin: https://panel.sanjagh.com",
        "Connection: keep-alive",
        "Referer: https://panel.sanjagh.com/ads/new",
        "Pragma: no-cache",
        "Cache-Control: no-cache"
    ];
    public $fields = [
        "campaign_id" => "",
        "title" => "",
        "platform" => "web",
        "ad_format" => "push_notification",
        "url" => "",
        "budget_type" => "cpm",
        "cost" => "4500",
        "description" => "",
        "speed" => "10",

        // UTMs
        "utm_type" => "2",
        "utm_source" => "sanjagh",
        "utm_medium" => "push_notification",
        "utm_campaign" => "campaign_id",
        "utm_campaign_type" => "1",
        "utm_term" => "ad_id",
        "utm_term_type" => "2",
        "utm_content" => "media_name",
        "utm_content_type" => "0"
    ];
    public $files;

    
    public function __construct($data , $sites){
        $this->build_http_data();
        $this->build_img_data($data["img"]);

        foreach($sites as $site){
            switch ($site){
                case "rokna":
                    $this->fields["campaign_id"] = self::RK_CAMP_ID;
                    $this->fields["title"] = $data["title"];
                    $this->fields["url"] = $data["url"];
                    $this->fields["description"] = $data["desc"];
                    $post_data = $this->build_data_files($this->boundary , $this->fields , $this->files);
                    array_push(
                        $this->headers,
                        "Content-Length: " . strlen($post_data)
                    );
                    // CURL to Sanjagh
                    $this->curl($post_data);
                    break;
                case "shock":
                    $this->fields["campaign_id"] = self::SHK_CAMP_ID;
                    $this->fields["title"] = $data["title"];
                    $this->fields["url"] = $data["url"];
                    $this->fields["description"] = $data["desc"];

                    $post_data = $this->build_data_files($this->boundary , $this->fields , $this->files);

                    array_push(
                        $this->headers,
                        "Content-Length: " . strlen($post_data)
                    );

                    // CURL to Sanjagh
                    $this->curl($post_data);
                    break;
                case "cinemabartar":
                    $this->fields["campaign_id"] = self::CB_CAMP_ID;
                    $this->fields["title"] = $data["title"];
                    $this->fields["url"] = $data["url"];
                    $this->fields["description"] = $data["desc"];

                    $post_data = $this->build_data_files($this->boundary , $this->fields , $this->files);

                    array_push(
                        $this->headers,
                        "Content-Length: " . strlen($post_data)
                    );

                    // CURL to Sanjagh
                    $this->curl($post_data);
                    
                    break;
                default:
                    echo " The Destination Site doesn't suppoerted...!";
            }
        }
    }

    public function build_img_data($img){
        $this->crop_img($img , "temp/cropped_media.jpg" , 480 , 240);
        $this->crop_img($img , "temp/cropped_icon.jpg" , 120 , 120);

        if($img == "default/default_media.jpg"){
            $filenames = [
                "icon" => 'default/default_icon.jpg',
                "media" => 'default/default_media.jpg'
            ];
        }else{
            $filenames = [
                "icon" => 'temp/cropped_icon.jpg',
                "media" => 'temp/cropped_media.jpg'
            ];
        }

        $files = array();
        foreach ($filenames as $n =>$f){
        $files[$n] = file_get_contents($f);
        }

        $this->files = $files;
    }

    public function build_http_data(){
        $this->boundary = uniqid();
        $this->delimiter = '-------------' . $this->boundary;
        array_push(
            $this->headers,
            "Content-Type: multipart/form-data; boundary=" . $this->delimiter
        );
    }

    public function curl($post_data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->snj_url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);

        //CURL SSL Disabling
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST , 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , 0);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION , 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);

        // CURL HEADERS
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:82.0) Gecko/20100101 Firefox/82.0");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        // CURL POST Methode data sending
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        // CURL HTTP Version 1 for sending Multipart Files to server
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        // CURL Rokna PUSH
        $response = curl_exec($ch);
        $rk_response_httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_info = curl_getinfo($ch);
        if($curl_error = curl_error($ch)){
            $req_count = 0;
            do{
                $response_url = curl_exec($ch);
                $req_count++;
            }while( curl_error($ch) || $req_count < 2);
        }
        if($curl_error = curl_error($ch)){
            echo "Rokna PUSH Failed, Error: <br>";
            var_dump($curl_error);
            echo "Rokna PUSH Failed, Curl Info: <br>";
            var_dump($curl_info);
        }

    }


    public function crop_img($main_image , $final_image_name , $final_width , $final_height){
        if(!isset($main_image)){
            return "give a img";
        }
        if(!isset($final_width)){
            return "give a width";
        }
        if(!isset($final_height)){
            return "give a height";
        }
    
        copy ($main_image, 'temp'.DIRECTORY_SEPARATOR.'post_image.jpg');
        $org_image = imagecreatefromjpeg('temp'.DIRECTORY_SEPARATOR.'post_image.jpg');
    
        $width = imagesx($org_image);
        $height = imagesy($org_image);
    
        $orginal_aspect = $width / $height;
        $final_aspect = $final_width / $final_height;
    
        if($orginal_aspect >= $final_aspect){
            $new_height = $final_height;
            // $new_width = $width / ($height / $final_height);
            $new_width = intval($width / ($height / $final_height));
        }else{
            $new_width = $final_width;
            // $new_height = $height / ($width / $final_width);
            $new_height = intval($height / ($width / $final_width));
        }
    
        $final_image = imagecreatetruecolor($final_width , $final_height);
    
        @imagecopyresampled(
            $final_image,
            $org_image,
            0 - ($new_width - $final_width) / 2,
            0 - ($new_height - $final_height) / 2,
            0,
            0,
            $new_width, $new_height,
            $width, $height
        );
        if(imagejpeg($final_image, $final_image_name, 80)){
            return true;
        }
    }

    public function build_data_files($boundary, $fields, $files){
        $data = '';
        $eol = "\r\n";
        $delimiter = '-------------' . $boundary;
    
        foreach ($fields as $name => $content) {
            $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="' . $name . "\"".$eol.$eol
                . $content . $eol;
        }
        foreach ($files as $name => $content) {
            $data .= "--" . $delimiter . $eol
                . 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $name . '"' . $eol
                //. 'Content-Type: image/png'.$eol
                . 'Content-Transfer-Encoding: binary'.$eol
                ;
    
            $data .= $eol;
            $data .= $content . $eol;
        }
        $data .= "--" . $delimiter . "--".$eol;
        return $data;
    }
}


class push_to_pushe{
    private $RK_TOKEN;
    private $RK_APP_ID;

    private $EGHT100_TOKEN;
    private $EGHT100_APP_ID;

    private $EGHTIR_TOKEN;
    private $EGHTIR_APP_ID;

    private $KHT_TOKEN;
    private $KHT_APP_ID;

    private $TOKEN;
    private $APP_ID;

    public $req_url;
    public $site;
    public $data;
    public $push_url;
    public $json_data;

    function __construct($req_url){
        $this->fillCredentialsFromSource();
        $this->req_url = $req_url;
        $this->site = new rec_driver($req_url);
        $this->site = $this->site->site;
        
        switch ($this->site) {
            case 'rokna':
                $this->data = new grab_data_rk($this->req_url);
                $this->TOKEN = $this->RK_TOKEN;
                $this->APP_ID = $this->RK_APP_ID;
                break;
            case 'eghtesadeirani':
                $this->data = new grab_data_eght($this->req_url);
                $this->TOKEN = $this->EGHTIR_TOKEN;
                $this->APP_ID = $this->EGHTIR_APP_ID;
                break;
            case 'khatesalamat':
                $this->data = new grab_data_kht($this->req_url);
                $this->TOKEN = $this->KHT_TOKEN;
                $this->APP_ID = $this->KHT_APP_ID;
                break;
            default:
                break;
        }

        if($this->data->short_url){
            $this->push_url = $this->data->short_url;
        }else{
            $this->push_url = $this->data->url;
        }
    }

    protected function fillCredentialsFromSource() {
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );  
        $config = file_get_contents("https://artstage.ir/pushe_config", false, stream_context_create($arrContextOptions));
        $master_config = json_decode($config);
        $tokens = (array) $master_config->tokens;

        foreach ($tokens as $index => $value) {
            $this->$index = $value;
            // error_log("__________ this->$index seted to " . $this->$index . " __________");
        }
    }

    // JSON Create Function
    public function create_json($data , $push_url , $APP_ID){
        $json_data = '{
            "data":{
                    "title":"'.$data->title.'",
                    "content":"'.$data->desc.'",
                    "icon":"'.$data->img.'",
                    "action":{
                        "id":"open_link",
                        "action_type":"U",
                        "params":{
                            "url":"https://rokna.net"
                        },
                        "url":"'.$push_url.'"
                    },
                    "image":"'.$data->img.'",
                    "close_on_click":false
            },
            
            "app_ids": ["'.$APP_ID.'"],
            "time_to_live":604800,
            "data_type":1,
            "is_draft":true,
            "platform":2
        }';

        $this->json_data = $json_data;
    }

    // Curl Function to Send data to Pushe API
    public function push_fn($json_data , $TOKEN){
        $ch = curl_init('https://api.pushe.co/v2/messaging/notifications/');
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'https://api.pushe.co/v2/messaging/notifications',

            // SSL OFF
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,

            CURLOPT_POST  => 1,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Token ".$TOKEN,
            ),
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function push(){
        if($this->site == "eghtesadeirani"){
            $this->create_json($this->data , $this->push_url , $this->EGHTIR_APP_ID);
            $res = $this->push_fn($this->json_data , $this->TOKEN);
            $this->create_json($this->data , $this->push_url , $this->EGHT100_APP_ID);
            $res = $this->push_fn($this->json_data , $this->TOKEN);
        }else{
            $this->create_json($this->data , $this->push_url , $this->APP_ID);
            $res = $this->push_fn($this->json_data , $this->TOKEN);
        }
        return $res;
    }
}