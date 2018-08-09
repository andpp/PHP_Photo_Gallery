<?php 
// error_reporting (E_ALL ^ E_NOTICE);
// photo gallery settings
$mainFolder    = 'albums';   // folder where your albums are located - relative to root
$albumsPerPage = '64';       // number of albums per page
$itemsPerPage  = '128';      // number of images per page    
$thumb_width   = '150';      // width of thumbnails
//$thumb_height  = '85';       // height of thumbnails
$extensions    = array(".jpg",".png",".gif",".JPG",".PNG",".GIF"); // allowed extensions in photo gallery

/*
// create thumbnails from images
function make_thumb($folder,$src,$dest,$thumb_width) {

	$source_image = imagecreatefromjpeg($folder.'/'.$src);
	$width = imagesx($source_image);
	$height = imagesy($source_image);
	
	$thumb_height = floor($height*($thumb_width/$width));
	
	$virtual_image = imagecreatetruecolor($thumb_width,$thumb_height);
	
	imagecopyresampled($virtual_image,$source_image,0,0,0,0,$thumb_width,$thumb_height,$width,$height);
	
	imagejpeg($virtual_image,$dest,100);
	
}
*/

// display pagination
function print_pagination($numPages,$urlVars,$currentPage) {
        
   if ($numPages > 1) {
       echo 'Page '. $currentPage .' of '. $numPages;
       echo '&nbsp;&nbsp;&nbsp;';

       if ($currentPage > 1) {
          $prevPage = $currentPage - 1;
          echo '<a href="?'. $urlVars .'p='. $prevPage.'">&laquo;&laquo;</a> ';
       }	   
	   
       for( $e=0; $e < $numPages; $e++ ) {
           $p = $e + 1;
       
           if ($p == $currentPage) {	    
              $class = 'current-paginate';
           } else {
              $class = 'paginate';
           } 
           echo '<a class="'. $class .'" href="?'. $urlVars .'p='. $p .'">'. $p .'</a>';
       }
	   
       if ($currentPage != $numPages) {
           $nextPage = $currentPage + 1;	
           echo ' <a href="?'. $urlVars .'p='. $nextPage.'">&raquo;&raquo;</a>';
       }	  	 
   }

}

function find_image($dirs) {
    global $extensions;

    for ($i = 1; $i <= 10; $i++) {
        $rand_pic  = $dirs[array_rand($dirs)];
        $ext = strrchr($rand_pic, '.');
        if(in_array($ext, $extensions)) {
            $i=20;
        }
    }
    return $rand_pic;

}

function get_thumb($name) {
    $th_name = str_replace("albums","thumbs",$name);
    $th_name = str_replace("//","/",$th_name);
    if (file_exists($th_name)) {
	return $th_name;
    }
    return '';
}


function get_subdirs($parent) {

    global $mainFolder;

    // display list of albums
    $folders = scandir($mainFolder . '/' . $parent, 0);
    $ignore  = array('.', '..', 'thumbs');
		  
    $albums = array();
    $captions = array();
    $random_pics = array();

    foreach($folders as $album) {
        if(!in_array($album, $ignore) and is_dir($mainFolder . '/' . $parent . '/' . $album)) {    
		 
 	    array_push( $albums, $parent . '/' . $album );
			 
	    $caption = substr($album,0,30);
	    array_push( $captions, $caption );
			 
	    //$rand_dirs = glob($mainFolder.'/'.$album.'/thumbs/*.*', GLOB_NOSORT);
	    $rand_dirs = glob($mainFolder . '/' . $parent.'/'.$album.'/*.*', GLOB_NOSORT);
	    if( count($rand_dirs) != 0) {
                $rand_pic = find_image($rand_dirs);
	    } else {
		$rand_dirs = glob($mainFolder . '/' . $parent.'/'.$album.'/*/*.*', GLOB_NOSORT);
		if( count($rand_dirs) != 0) {
                   $rand_pic  = find_image($rand_dirs);
                } else {
                    $rand_pic = 'none';
		}
	    }
	    array_push( $random_pics, $rand_pic );
	}
    }
    return array($albums, $random_pics, $captions);
}

function print_albums($albums, $random_pics, $captions) {
    global $albumsPerPage;       // number of albums per page
    global $itemsPerPage;      // number of images per page    
    global $thumb_width;

    if( count($albums) == 0 ) {
        # echo 'There are currently no albums.';     
    } else {
	$numPages = ceil( count($albums) / $albumsPerPage );

        if(isset($_GET['p'])) {
            $currentPage = $_GET['p'];
            if($currentPage > $numPages) {
               $currentPage = $numPages;
            }
        } else {
            $currentPage=1;
        } 
 
        $start = ( $currentPage * $albumsPerPage ) - $albumsPerPage;
/*	
	echo '<div class="titlebar">
                <div class="float-left"><span class="title">Albums: ' . $_GET['album'] . '</span> <a href="'.$_SERVER['PHP_SELF'].'">View All Albums</a></div>
                <div class="float-right">'.count($albums).' albums</div>
		</div>';
*/
	print_titlebar(count($albums), true);
        echo '<div class="clear"></div>';
	for( $i=$start; $i<$start + $albumsPerPage; $i++ ) {
            if( isset($albums[$i]) ) {		 		 			 
               echo '<div class="thumb-album shadow">		        
                       <div class="thumb-wrapper">
			 <a href="'.$_SERVER['PHP_SELF'].'?album='. urlencode($albums[$i]) .'">';
               $th_name = get_thumb($random_pics[$i]);
               if ( $th_name == '' ) {
                 echo       '<img src="thumb.php?f='. $random_pics[$i] .'" width="'.$thumb_width.'" alt="" />';
               } else {
                 echo       '<img src="'. $th_name .'" width="'.$thumb_width.'" alt="" />';
               }
               echo         ' </a>	
                      </div>
                      <div class="p5"></div>
                        <a href="'.$_SERVER['PHP_SELF'].'?album='. urlencode($albums[$i]) .'">
                          <span class="caption">'. $captions[$i] .'</span>
                        </a>  
                      </div>';
	    }	  	  
	}
	echo '<div class="clear"></div>';
        echo '<div align="center" class="paginate-wrapper">';
        $urlVars = "";
        print_pagination($numPages,$urlVars,$currentPage);
        echo '</div>';	   
     }
}

function get_files($mainFolder, $alb) {

     global $thumb_width;
     global $extensions;

     // display photos in album
     $src_folder = $mainFolder.'/'.$alb;
     $src_files  = scandir($src_folder);
 
     $files = array();

     foreach($src_files as $file) {
        $ext = strrchr($file, '.');
        if(in_array($ext, $extensions)) {
	   array_push( $files, $file );
/*		   
	   if (!is_dir($src_folder.'/thumbs')) {
              mkdir($src_folder.'/thumbs');
              chmod($src_folder.'/thumbs', 0777);
              //chown($src_folder.'/thumbs', 'apache'); 
           }
		   
	   $thumb = $src_folder.'/thumbs/'.$file;
           if (!file_exists($thumb)) {
              make_thumb($src_folder,$file,$thumb,$thumb_width); 
          
	   }
*/
       }
     }

     return array($files,  $src_folder);
}

function print_titlebar($cnt, $is_albums = false) {

      $alb = $_GET['album'];
      $alb = ltrim(str_replace("//","/",$alb),'/');
      $albs = explode ("/", $alb);

      echo '<div class="titlebar">';
      //      echo   ' <div class="float-left"><span class="title">'. $_GET['album'] .'</span> - <a href="'.$_SERVER['PHP_SELF'].'">View All Albums</a></div>';

      echo   ' <div class="float-left"><span class="title">';
      if ($is_albums)  echo 'Albums: '; 
      echo '<a href="'.$_SERVER['PHP_SELF'].'">Home</a>';
      for ($i=0; $i < count($albs)-1; $i++) {
        echo '<a href="'.$_SERVER['PHP_SELF'].'?album='. urlencode(join("/", array_slice($albs, 0, $i+1)))  .'">/'. $albs[$i] .'</a>';
      }
      echo '/' . $albs[count($albs)-1];
      echo   '</span></div>';
      echo   ' <div class="float-right">'. $cnt; 
      if ($is_albums) echo ' albums'; else echo ' images'; 
           echo '</div> </div>';      

}

function print_files($files, $src_folder) {
     global $albumsPerPage;       // number of albums per page
     global $itemsPerPage;      // number of images per page
     global $thumb_width;

    if ( count($files) == 0 ) {
       echo 'There are no photos in this album!';
    } else {
       $numPages = ceil( count($files) / $itemsPerPage );
       if(isset($_GET['p'])) {
          $currentPage = $_GET['p'];
          if($currentPage > $numPages) {
              $currentPage = $numPages;
          }
      } else {
         $currentPage=1;
      } 

      echo '<div class="clear"></div>';
      echo '<div align="center" class="paginate-wrapper">';
      $urlVars = "album=".urlencode($_GET['album'])."&amp;";
      print_pagination($numPages,$urlVars,$currentPage);
      echo '</div>';

      $start = ( $currentPage * $itemsPerPage ) - $itemsPerPage;

/*
      $alb = $_GET['album'];
      $alb = ltrim(str_replace("//","/",$alb),'/');
      $albs = explode ("/", $alb);

      echo '<div class="titlebar">';
      //      echo   ' <div class="float-left"><span class="title">'. $_GET['album'] .'</span> - <a href="'.$_SERVER['PHP_SELF'].'">View All Albums</a></div>';

      echo   ' <div class="float-left"><span class="title">';
      echo '<a href="'.$_SERVER['PHP_SELF'].'">Home</a>';
      for ($i=0; $i < count($albs)-1; $i++) {
        echo '<a href="'.$_SERVER['PHP_SELF'].'?album='. urlencode(join("/", array_slice($albs, 0, $i+1)))  .'">/'. $albs[$i] .'</a>';
      }
      echo '/' . $albs[count($albs)-1];
      echo   '</span></div>';
      echo   ' <div class="float-right">'.count($files).' images</div>
	      </div>';	  
*/
      print_titlebar(count($files));
      echo '<div class="clear"></div>';
      for( $i=$start; $i<$start + $itemsPerPage; $i++ ) {
         if( isset($files[$i]) && is_file( $src_folder .'/'. $files[$i] ) ) { 
            echo '<div class="thumb shadow">
                    <div class="thumb-wrapper">
		       <a href="img.php?f=' . $src_folder .'/'. $files[$i] .'" class="albumpix" rel="albumpix">';
	    $th_name = get_thumb($src_folder .'/'. $files[$i]);
            if ( $th_name == '' ) {
               echo     '<img src="thumb.php?f='. $src_folder .'/'. $files[$i] .'" width="'.$thumb_width.'" alt="" />';
	    } else {
	       echo     '<img src="'. $th_name .'" width="'.$thumb_width.'" alt="" />';
	    }
            echo      '</a>
                   </div>  
                 </div>'; 
         } else {
             if( isset($files[$i]) ) {
                echo $files[$i];
             }
         }
      }
      echo '<div class="clear"></div>';
      echo '<div align="center" class="paginate-wrapper">';
      $urlVars = "album=".urlencode($_GET['album'])."&amp;";
      print_pagination($numPages,$urlVars,$currentPage);
      echo '</div>';
   }
}

if (!isset($_GET['album'])) {
    $a = get_subdirs('');
    print_albums($a[0], $a[1], $a[2]);
} else {
   $alb = $_GET['album'];
   
   $a = get_subdirs($alb);
   print_albums($a[0], $a[1], $a[2]);

   $a=get_files($mainFolder, $alb);
   print_files($a[0], $a[1]);
}
?>
