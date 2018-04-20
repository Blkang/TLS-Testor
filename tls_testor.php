<!DOCTYPE html>
<html lang="en">
  <head>
    <title>TLS Testor</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.css" rel="stylesheet">
  </head>
  <body>
    <section>
      <div class="container">
        <div class="row">
          <h1>TLS Testor</h1>
          <hr />
          <?php
            $mtime = microtime();
            $mtime = explode(' ' ,$mtime);
            $mtime = $mtime[1] + $mtime[0];
            $starttime = $mtime;

            echo '<h2>Server configuration:</h2>';;

            if (!defined('PHP_VERSION_ID')) {
                        $version = explode('.', PHP_VERSION);
                        define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
            }
            $is_php_up2date = PHP_VERSION_ID < 50300 ? false : true;
            
            $curl_exists = extension_loaded('curl');
            if ($curl_exists) {
                    $curl_version = curl_version();
              $is_curl_up2date = version_compare($curl_version['version'], '7.21', '>') ? true : false;
            }

          $openssl_exists = extension_loaded('openssl');
          if ($openssl_exists) {
            $matches = array();
            $openssl_version = OPENSSL_VERSION_NUMBER;
            $openssl_text = OPENSSL_VERSION_TEXT;
            $is_openssl_up2date = OPENSSL_VERSION_NUMBER > 0x1000100f ? true : false;
          }


          $ch_tls = curl_init('https://www.howsmyssl.com/a/check'); 
          curl_setopt($ch_tls, CURLOPT_RETURNTRANSFER, true); 
          $data_tls = curl_exec($ch_tls); 
          curl_close($ch_tls); 
          $json_tls = json_decode($data_tls);
          $tls_version = $json_tls->tls_version;

          echo '<ul class="list-group">';
          echo '<li class="list-group-item"><strong>Domain:</strong> ';
          print_r($_SERVER['SERVER_NAME']);
          echo '</li>';
          
          echo '<li class="list-group-item"><strong>SSL Server:</strong> <a href="https://www.ssllabs.com/ssltest/analyze.html?d='. $_SERVER['SERVER_NAME'] .'" target="_blank"> <img src="https://sslbadge.org/?domain='. $_SERVER['SERVER_NAME'] .'" /></a>';
          echo ' <span class="dropdown"><button class="btn btn-default btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">More <span class="caret"></span>';
          echo '</button>';
          echo '<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">';
          echo '<li class="dropdown-header">SSL testing tools</li>';
          echo '<li><a href="https://www.ssllabs.com/ssltest/analyze.html?d='. $_SERVER['SERVER_NAME'] .'" target="_blank">Qualys SSL Labs</a></li>';
          echo '<li><a href="https://ssldecoder.org/?host='. $_SERVER['SERVER_NAME'] .'" target="_blank">SSL Decoder</a></li>';
          echo '<li><a href="https://cryptoreport.websecurity.symantec.com/checker/" target="_blank">Symantec.cryptoreport</a></li>';
          echo '</ul>';
          echo '</span>';
          echo '</li>';

          echo '<li class="list-group-item"><strong>Security:</strong> <a href="https://securityheaders.io/?q='. $_SERVER['SERVER_NAME'] .'&hide=on&followRedirects=on" target="_blank"><img src="https://securityheadersiobadges.azurewebsites.net/create/badge?domain=https://'. $_SERVER['SERVER_NAME'] .'"></a></li>';
          
          echo '<li class="list-group-item"><strong>PHP: </strong>';
          print_r(PHP_VERSION);
          if ($is_php_up2date) {
            echo ' - PHP version is good <i class="fa fa-check-circle-o" aria-hidden="true"></i>';
          } else {
            echo ' - PHP version is too old <i class="fa fa-exclamation-circle" aria-hidden="true"></i>';
          }
          echo '</li>';
          
          echo '<li class="list-group-item"><strong>cURL: </strong> '. $curl_version['version'];
          if ($curl_exists) {
            if ($is_curl_up2date) {
              echo ' - cURL version is good <i class="fa fa-check-circle-o" aria-hidden="true"></i>';
            } else {
              echo ' - cURL version is too old <i class="fa fa-exclamation-circle" aria-hidden="true"></i>';
            }
          } else {
            echo 'cURL is not installed <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>';
          }
          echo '</li>';
          
          echo '<li class="list-group-item"><strong>OpenSSL: </strong>'. $openssl_text .'';
          if ($openssl_exists) {
            if ($is_openssl_up2date) {
              echo ' - OpenSSL version is good <i class="fa fa-check-circle-o" aria-hidden="true"></i>';
            } else {
              echo ' - OpenSSL version is too old <i class="fa fa-exclamation-circle" aria-hidden="true"></i>';
            }
          } else {
            echo 'OpenSSL is not installed <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>';
          }
          echo '</li>';
          if ($openssl_exists) {
            if ($tls_version == 'TLS 1.2') {
              echo '<li class="list-group-item alert alert-success"><strong>TLS version: </strong>' . $tls_version . ' - TLS version is good, you have nothing to do <i class="fa fa-check-circle-o" aria-hidden="true"></i></li>';
            } elseif ($tls_version == 'TLS 1.3') {
              echo '<li class="list-group-item alert alert-success"><strong>TLS version: </strong>' . $tls_version . ' - TLS version is good, you have nothing to do <i class="fa fa-check-circle-o" aria-hidden="true"></i></li>';
            } else {
              echo '<li class="list-group-item alert alert-danger"><strong>TLS version: </strong>' . $tls_version . ' - TLS version is too old, please upgrade to TLS 1.2 or TLS 1.3 <i class="fa fa-exclamation-circle" aria-hidden="true"></i></li>';
            }
          } else {
            echo '<li class="list-group-item alert alert-warning">OpenSSL is not installed <i class="fa fa-exclamation-triangle" aria-hidden="true"></i></li>';
          }
          echo '</ul>';

          $mtime = microtime();
          $mtime = explode(" ",$mtime);
          $mtime = $mtime[1] + $mtime[0];
          $endtime = $mtime;
          $totaltime = ($endtime - $starttime);
          echo '<hr/><p class="text-center"><small>Total Time: '.$totaltime.' seconds</small></p>';


          ?>
        <a href="https://github.com/fbureau/TLS-Testor"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/e7bbb0521b397edbd5fe43e7f760759336b5e05f/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f677265656e5f3030373230302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_green_007200.png"></a>
        </div>
      </div>
    </section>

  </body>
</html>