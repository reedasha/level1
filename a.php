<?php

	$repo_name = $argv[1];
	$pos = strpos($repo_name, '/');

	$array = array();
	
	$url = 'https://api.bitbucket.org/2.0/repositories/' . $repo_name . '/pullrequests';

        $options = array('http' => array(
            'method'  => 'GET',
            'ignore_errors' => TRUE
        ));

    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if(is_array($http_response_header))
        {
            $parts=explode(' ',$http_response_header[0]);
            if(count($parts)>1) //HTTP/1.0 <code> <text>
                $code = intval($parts[1]); //Get code
        }

        
	//$content = file_get_contents('https://api.bitbucket.org/2.0/repositories/' .$author .'/' .$repo_slug .'/pullrequests');
    if($code == 200) {
            $hrefs = json_decode($response);
            foreach ($hrefs->values as $item)
            {
                if(property_exists ($item, 'links'))
                {
                    if(property_exists ($item->links,'html'))
                    {
                        if(property_exists ($item->links->html, 'href'))
                        {
                            $link = $item->links->html->href;
                            array_push($array, "-new-tab -url " .$link);
                        }
                    }
                }
            }
            $string = implode(" ", $array);
            exec("google-chrome " .$string);
            
        }
        else {
            switch($code){
                case 403:
                    print("You need to authorize to access private repository\r\n");
                    break;
                case 404:
                    print("No such repository found\r\n");
                    break;
            }
        }
?>
