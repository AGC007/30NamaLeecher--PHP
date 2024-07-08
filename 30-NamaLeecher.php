<?php

#~~~~~~~ Var Set ~~~~~~~#

define('Api_Key' ,'-- 30Nama Account ApiKey --');
define('Token' ,'-- 30Nama Account Token --');

#~~~~~~~ Var Set ~~~~~~~#

#--------------- GetPageID ---------------#

if($_REQUEST['page'])
{

    $preg_s_1 = preg_split("/https:\/\/30nama.com\//", $_REQUEST['page']);
    $preg_s_2 = preg_split("/\//", $preg_s_1[1]);
    $MovieID = $preg_s_2[1];

    f_30NamaLeecher(Api_Key, Token , $MovieID);

}
#--------------- GetPageID ---------------#

#--------------- 30NamaLeecher ---------------#

function f_30NamaLeecher($C_Api_Key , $C_Token , $MovieID)
{
    #``````````` HEADER ```````````#

    $HEADER = array(
        'Accept: application/json, text/plain, */*',
        //'Accept-Encoding: gzip, deflate, br, zstd',
        'Accept-Language: en-US,en;q=0.9,fa;q=0.8',
        'C-Api-Key: '.$C_Api_Key,
        'C-App-Version: 2.0.0',
        'C-Platform: Website',
        'C-Token: '.$C_Token,
        'C-Useragent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 Edg/126.0.0.0',
        'Content-Length: 2',
        'Content-Type: application/json',
        'Origin: https://30nama.com',
        'Referer: https://30nama.com/',
        'Sec-Ch-Ua-Mobile: ?0',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: same-site',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 Edg/126.0.0.0',
    );

    #``````````` HEADER ```````````#

#--------------- LOGIN ---------------#

    $REQ_LOGIN = curl_init();

    curl_setopt($REQ_LOGIN, CURLOPT_URL, 'https://interface.30nama.com/api/v1/action/user');
    curl_setopt($REQ_LOGIN, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($REQ_LOGIN, CURLOPT_POSTFIELDS, "{}");
    #curl_setopt($REQ_LOGIN, CURLOPT_HEADER, 1);
    curl_setopt($REQ_LOGIN, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($REQ_LOGIN, CURLOPT_HTTPHEADER, $HEADER);

    $RES_LOGIN_JSON = json_decode(curl_exec($REQ_LOGIN) , TRUE);

     if($RES_LOGIN_JSON['result']['allowed_to_download'] == '1') // Check Subscribe
     {
         #--------------- Get_Data_Movie ---------------#

         $REQ_MOVIE_DATA = curl_init();

         curl_setopt($REQ_MOVIE_DATA, CURLOPT_URL, 'https://interface.30nama.com/api/v1/action/single/id/'.$MovieID);
         curl_setopt($REQ_MOVIE_DATA, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($REQ_MOVIE_DATA, CURLOPT_POSTFIELDS, "{}");
         #curl_setopt($REQ_MOVIE_DATA, CURLOPT_HEADER, 1);
         curl_setopt($REQ_MOVIE_DATA, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
         curl_setopt($REQ_MOVIE_DATA, CURLOPT_HTTPHEADER, $HEADER);

         $RES_MOVIE_DATA_JSON = json_decode(curl_exec($REQ_MOVIE_DATA) , TRUE);

           $MOVIE_TITLE = $RES_MOVIE_DATA_JSON['result']['title'];
           $MOVIE_TYPE = $RES_MOVIE_DATA_JSON['result']['is_series'];
           $MOVIE_YEAR = $RES_MOVIE_DATA_JSON['result']['year'];
           $MOVIE_GENRE = $RES_MOVIE_DATA_JSON['result']['genre'][0];
           $MOVIE_COUNTRY = $RES_MOVIE_DATA_JSON['result']['country'][0]['name'];
           $MOVIE_TIME = $RES_MOVIE_DATA_JSON['result']['time'];
           $MOVIE_IMDB = $RES_MOVIE_DATA_JSON['result']['imdb_score'];
           $MOVIE_POSTER = $RES_MOVIE_DATA_JSON['result']['image']['poster']['big'];
           $MOVIE_DIRECTOR = $RES_MOVIE_DATA_JSON['result']['director'][0]['name'];


           if($MOVIE_TYPE == '0') // Check Movie Type
           {
               #--------------- Get_Download_link(Movie) ---------------#

               $REQ_MOVIE_DOWN_LINK = curl_init();

               curl_setopt($REQ_MOVIE_DOWN_LINK, CURLOPT_URL, 'https://interface.30nama.com/api/v1/action/download/id/'.$MovieID);
               curl_setopt($REQ_MOVIE_DOWN_LINK, CURLOPT_RETURNTRANSFER, 1);
               curl_setopt($REQ_MOVIE_DOWN_LINK, CURLOPT_POSTFIELDS, "{}");
               #curl_setopt($REQ_MOVIE_DOWN_LINK, CURLOPT_HEADER, 1);
               curl_setopt($REQ_MOVIE_DOWN_LINK, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
               curl_setopt($REQ_MOVIE_DOWN_LINK, CURLOPT_HTTPHEADER, $HEADER);

               //echo curl_exec($REQ_MOVIE_DOWN_LINK);
               $RES_MOVIE_DOWN_LINK_JSON = json_decode(curl_exec($REQ_MOVIE_DOWN_LINK) , TRUE);

               $dl_Movie_Count =  count($RES_MOVIE_DOWN_LINK_JSON['result']['download']);


               for($i=0; $i <= $dl_Movie_Count - 1; $i++)//Get Download List Link
               {
                   $dl_Movie_Quality[$i] =  $RES_MOVIE_DOWN_LINK_JSON['result']['download'][$i]['quality'];
                   $dl_Movie_Size[$i] = $RES_MOVIE_DOWN_LINK_JSON['result']['download'][$i]['size'];
                   $dl_Movie_Encoder[$i] = $RES_MOVIE_DOWN_LINK_JSON['result']['download'][$i]['encoder'];
                   $dl_Movie_Link[$i] =  $RES_MOVIE_DOWN_LINK_JSON['result']['download'][$i]['dl'];
               }

               #~~~~ Movie Json ~~~~#

               echo(json_encode(array(

                    'code' => http_response_code(),
                    'message' => 'success' ,
                    'developer' => 'AGC007',

                   'data' =>   array(

                   'MovieName' => $MOVIE_TITLE ,
                   'isSeries' => $MOVIE_TYPE ,
                   'MovieYear' => $MOVIE_YEAR ,
                   'MovieGenre' => $MOVIE_GENRE ,
                   'MovieCountry' => $MOVIE_COUNTRY ,
                   'MovieTime' => $MOVIE_TIME ,
                   'MoviePoster' => $MOVIE_POSTER ,
                   'MovieIMDB' => $MOVIE_IMDB ,
                   'MovieDirector' => $MOVIE_DIRECTOR ,

                   'dl' => array(

                   'DL_Movie_Quality' => $dl_Movie_Quality ,
                   'DL_Movie_Size' => $dl_Movie_Size ,
                   'DL_Movie_Encoder' => $dl_Movie_Encoder ,
                   'DL_Movie_Link' => $dl_Movie_Link ,
                   'Developer' => "AGC007"
               )))));

               #~~~~ Movie Json ~~~~#

           }
            elseif($MOVIE_TYPE == '1')// Check Movie Type
           {
                #--------------- Get_Download_link(Series) ---------------#

               $REQ_SERIES_DOWN_LINK = curl_init();

               curl_setopt($REQ_SERIES_DOWN_LINK, CURLOPT_URL, 'https://interface.30nama.com/api/v1/action/download/id/'.$MovieID);
               curl_setopt($REQ_SERIES_DOWN_LINK, CURLOPT_RETURNTRANSFER, 1);
               curl_setopt($REQ_SERIES_DOWN_LINK, CURLOPT_POSTFIELDS, "{}");
               #curl_setopt($REQ_SERIES_DOWN_LINK, CURLOPT_HEADER, 1);
               curl_setopt($REQ_SERIES_DOWN_LINK, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
               curl_setopt($REQ_SERIES_DOWN_LINK, CURLOPT_HTTPHEADER, $HEADER);

               // curl_exec($REQ_SERIES_DOWN_LINK);
               $RES_SERIES_DOWN_LINK_JSON = json_decode(curl_exec($REQ_SERIES_DOWN_LINK) , TRUE);

               $SERIES_SEASONS = $RES_SERIES_DOWN_LINK_JSON['result']['seasons'];

               #~~~~~ HTML SOURCE ~~~~~#
               ?>

               <html style="text-align: center;background-color: black; color:white;background-image: url('https://agc007.top/AGC007/Robot/KingMovieLeecher/KingMovieService/backiee-252055.jpg');" >
               <title>30-NamaLeecher [SD] By AGC007</title>

               <img style="height: 300px;width: 300px; border-radius:30px; margin-bottom:8px;" src=<?php echo($MOVIE_POSTER)  ?>>
               </br>
               <a style="background-color:darkslategrey;">- SerialName : <?php echo($MOVIE_TITLE.$MOVIE_YEAR) ?> -</a>
               </br>
               <a style="background-color:darkslategrey;">- SerialDirector : <?php echo($MOVIE_DIRECTOR) ?> -</a>
               </br>
               <a style="background-color:darkslategrey;">- SerialIMDB : <?php echo($MOVIE_IMDB) ?> -</a>
               </br>
               <a style="background-color:darkslategrey;">- SerialSeasons : <?php echo($SERIES_SEASONS) ?> -</a>
               </br>
               </html>

               <?php

               #~~~~~ HTML SOURCE ~~~~~#

               $dl_SERIES_Count =  count($RES_SERIES_DOWN_LINK_JSON['result']['download']);

               for($A=0; $A <= $dl_SERIES_Count - 1; $A++)//Go To List & Get DATA
               {
                   echo "</br>";

                   echo $dl_Series_Season[$A] = "Season : " . $RES_SERIES_DOWN_LINK_JSON['result']['download'][$A]['season'] ." - ";
                   echo $dl_Series_Episode[$A] = "Last Episode : " . $RES_SERIES_DOWN_LINK_JSON['result']['download'][$A]['last_episode'];

                   echo "</br>";
                   echo $dl_Series_Quality[$A] = "Quality : " . $RES_SERIES_DOWN_LINK_JSON['result']['download'][$A]['quality'];
                   echo "</br>";
                   echo $dl_Series_Size[$A] = "Size : " . $RES_SERIES_DOWN_LINK_JSON['result']['download'][$A]['size'];
                   echo "</br>";
                   echo $dl_Series_Encoder[$A] = "Encoder : " . $RES_SERIES_DOWN_LINK_JSON['result']['download'][$A]['encoder'];
                   echo "</br>";

                   $dl_SERIES_Episode_Count =  count($RES_SERIES_DOWN_LINK_JSON['result']['download'][$A]['link']);

                   for($B=0; $B <= $dl_SERIES_Episode_Count - 1;$B++)//Go To Download Episode List & Res
                   {

                       echo $dl_Series_Episode_Part[$B] = $RES_SERIES_DOWN_LINK_JSON['result']['download'][$A]['link'][$B]['episode']." : ";

                       $dl_Series_Episode_Source[$B] = $RES_SERIES_DOWN_LINK_JSON['result']['download'][$A]['link'][$B]['source'];

                       if($dl_Series_Episode_Source[$B] == null)
                       {
                           $dl_Series_Episode_Source[$B] = "Download Link ";
                       }

                       $dl_Series_Episode_Link[$B] = $RES_SERIES_DOWN_LINK_JSON['result']['download'][$A]['link'][$B]['dl'];

                       ?>
                       <a style="color:bisque;" href="<?php echo $dl_Series_Episode_Link[$B]; ?>"><?php echo $dl_Series_Episode_Source[$B]; ?> </a>
                       </br>

                       <?php
                   }
               }
               echo("</br> ~ Developer : AGC007 ~");
           }
           else {
               echo(json_encode(array(
                   'message' => 'خطا : مشکل در دریافت اطلاعات فیلم' ,
                   'developer' => 'AGC007',
               )));
           }
     }
     else {
         echo(json_encode(array(
             'message' => 'خطا : مشکل در ورود به اکانت یا اتمام اشتراک' ,
             'developer' => 'AGC007',
         )));
     }

}

#--------------- 30NamaLeecher ---------------#

?>