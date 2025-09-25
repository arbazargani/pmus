<?php

function pushe_upload($img , $TOKEN){
    $post_img = $img;
    // Crop Media Image to 480x240(px)
    crop_img($post_img , "temp/cropped_media.jpg" , 480 , 240);
    // Crop Icon Image to 480x240(px)
    crop_img($post_img , "temp/cropped_icon.jpg" , 120 , 120);

    // Files to Upload
    // Check for no img posts
    $files = array();
    if($post_img == "default/default_media.jpg"){
        $files = [
            "content" => file_get_contents('default/default_media.jpg'),
            "name" => "image",
            "filename" => "blob"
        ];
        $files_icon = [
            "content" => file_get_contents('default/default_icon.jpg'),
            "name" => "image",
            "filename" => "blob"
        ];
    }else{
        $files = [
            "content" => file_get_contents('temp/cropped_media.jpg'),
            "name" => "image",
            "filename" => "blob"
        ];
        $files_icon = [
            "content" => file_get_contents('temp/cropped_icon.jpg'),
            "name" => "image",
            "filename" => "blob"
        ];
    }

    $boundary = uniqid();
    $delimiter = '-----------------------------' . $boundary;
    $post_data = build_data_files($boundary , $files);
    $post_data_icon = build_data_files($boundary , $files_icon);

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.pushe.co/v2/files/icons/',
    CURLOPT_RETURNTRANSFER => true,

    // SSL OFF
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,

    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_HTTPHEADER => array(
        'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:109.0) Gecko/20100101 Firefox/111.0',
        'Accept: application/json, text/plain, */*',
        'Accept-Language: en-US,en;q=0.5',
        // 'Accept-Encoding: gzip, deflate, br',
        'Authorization: Token 6789c30fa6ec4649f41b3ccb6f3a2d8bb3e66bd4',
        "Content-Type: multipart/form-data; boundary=" . $delimiter,
        "Content-Length: " . strlen($post_data),
        'Origin: https://console.pushe.co',
        'Connection: keep-alive',
        'Referer: https://console.pushe.co/',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: same-site',
        'Pragma: no-cache',
        'Cache-Control: no-cache',
        'TE: trailers'
    ),
    ));

    // CURL POST Method data sending
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);

    $response = curl_exec($curl);

    $uploaded_files = array();

    $uploaded_files["img"] = json_decode($response , true);


    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data_icon);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:109.0) Gecko/20100101 Firefox/111.0',
        'Accept: application/json, text/plain, */*',
        'Accept-Language: en-US,en;q=0.5',
        // 'Accept-Encoding: gzip, deflate, br',
        'Authorization: Token facde45ba2d2cb1f3431844d7a4639abb8340c50',
        "Content-Type: multipart/form-data; boundary=" . $delimiter,
        "Content-Length: " . strlen($post_data_icon),
        'Origin: https://console.pushe.co',
        'Connection: keep-alive',
        'Referer: https://console.pushe.co/',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: same-site',
        'Pragma: no-cache',
        'Cache-Control: no-cache',
        'TE: trailers'
    ));
    $response2 = curl_exec($curl);
    $uploaded_files["icon"] = json_decode($response2 , true);

    error_log(json_encode($uploaded_files));
    error_log($response2);

    curl_close($curl);
    return $uploaded_files;
}

function crop_img($main_image , $final_image_name , $final_width , $final_height){

    $api_res = file_get_contents("https://artstage.ir/gd_api?img=$main_image");
    $master_res = json_decode($api_res);

    error_log($api_res);

    $cropped_image = $master_res->img;
    $cropped_icon = $master_res->icon;
    $main_cropped_img = copy ($cropped_image, 'temp'.DIRECTORY_SEPARATOR.'cropped_media.jpg');
    $main_cropped_icon = copy ($cropped_icon, 'temp'.DIRECTORY_SEPARATOR.'cropped_icon.jpg');
    

    if($main_cropped_img && $main_cropped_icon){
        // $org_image = imagecreatefromjpeg('temp'.DIRECTORY_SEPARATOR.'cropped_media.jpg');
        return true;
    }

    return false;
    



    if(!isset($main_image)){
        return "give a img";
    }
    if(!isset($final_width)){
        return "give a width";
    }
    if(!isset($final_height)){
        return "give a height";
    }

    $img_cont = file_get_contents($main_image);
    $org_image = imagecreatefromstring($img_cont);
    // file_put_contents('temp'.DIRECTORY_SEPARATOR.'post_image.jpg' , $org_image);

    // copy ($main_image, 'temp'.DIRECTORY_SEPARATOR.'post_image.jpg');
    // $org_image = imagecreatefromjpeg('temp'.DIRECTORY_SEPARATOR.'post_image.jpg');


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
function build_data_files($boundary, $file){
    $data = '';
    $eol = "\r\n";
    $delimiter = '-----------------------------' . $boundary;
    $data .= "--" . $delimiter . $eol
        . 'Content-Disposition: form-data; name="' . $file["name"] . '"; filename="' . $file["filename"] . '"' . $eol
        . 'Content-Type: image/jpg'.$eol
        // . 'Content-Transfer-Encoding: binary'.$eol
        ;

    $data .= $eol;
    $data .= $file["content"] . $eol;
    $data .= "--" . $delimiter . "--".$eol;
    return $data;
}
