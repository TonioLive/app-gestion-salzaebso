<?php
//------------------------------------------------------------------------------
//Design by    : M.G.Ranga Pathmasiri
//Author Email : itranga@hotmail.com
//Class Name   : Download_file
//Tested       : win 2000 , XP, IE, Opera, Netscape, Mozilla
//version      : 1.0;
//------------------------------------------------------------------------------



 error_reporting (0);

 class Download_file
{
  var $df_dirpath             =  "";
  var $df_contenttype         = "";
  var $df_contentdisposition  = "attachment";
  var $df_filename            = "";
  var $df_size                = 0;
  var $df_filefermisition     = 0;






  public function __construct(...$arguments) { $this->Download_file(...$arguments); }

function  Download_file($path, $file_name)
   {
     require_once __DIR__.'/safe-files.php';
     security_send_file(security_download_path($path,$file_name));

     if (($path) &&  ($file_name))
            {


                   $this->fileExsit($path, $file_name);
                   $this->fileInfo();
                   $this->fileType();
                   $this->init();


            }

     else
          {
            exit();
          }

   }

   function init()
    {
      ob_start();
          $this->download();
          ob_end_flush();
          ob_end_clean();
          $this->endExcute();


    }



 //  file exsit or not
    function   fileExsit($path, $file_name)
       {
           clearstatcache() ;
           $path = trim(stripslashes($path));


           if (is_dir($path))
               {
                  $this->df_dirpath  = $path . "/";

               }

           else
              {
                 echo "El archivo solicitado <font size = 4 color ='red'><u>$file_name</u></font> no se encuentra en el servidor.";
                                 $this->df_dirpath = NULL;
                 exit();

              }

           $file_name   = trim(stripslashes($file_name));
           $temp_fpath  =  $this->df_dirpath . $file_name;
           if (file_exists($temp_fpath))
               {
                    $this->df_filename = $temp_fpath;




               }
            else
               {
                               echo "El archivo solicitado <font size = 4 color ='red'><u>$file_name</u></font> no se encuentra en el servidor.";
                                   $this->df_filename = NULL;
                   exit();

              }


      }
 // Get file info
     function fileInfo()
         {
           if (($this->df_dirpath)  && ($this->df_filename))
               {
                  $this->df_size = filesize($this->df_filename);
                  $this->df_filefermisition = substr(decoct(fileperms($this->df_filename)), -1);


              }
            else
             {
                          $this->df_size = 0;
                          $this->df_filefermisition = 0;
              exit();
             }

        }
// Get file type
    function fileType()
       {
         if(($this->df_dirpath) && ($this->df_filename))
                  {
           $fname   = basename($this->df_filename);
           $ext     = substr($this->df_filename ,-3 );
             switch( $ext ){
                 case "pdf": $ctype="application/pdf";               break;
                 case "exe": $ctype="application/octet-stream";      break;
                               case "zip": $ctype="application/zip";               break;
                          case "doc": $ctype="application/msword";            break;
                           case "xls": $ctype="application/vnd.ms-excel";      break;
                           case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
                                case "gif": $ctype="image/gif";                     break;
                                case "png": $ctype="image/png";                     break;
                              case "jpg": $ctype="image/jpg";                     break;
                                 case "xml": $ctype="text/xml";                      break;
                                 default   : $ctype="application/force-download";
                      }
           $this->df_contenttype = $ctype;

          }

                }
//Download file
        function download()
        {
                      session_cache_limiter();
                          header("Pragma: public");
                  header("Expires: 0");
                  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                       header("Content-Type:" .$this->df_contenttype);
                                  header("Content-Disposition: ".$this->df_contentdisposition."; filename=".basename($this->df_filename));
                                  header("Content-Transfer-Encoding: binary");
                                  header("Content-Length: ".$this->df_size);

                                 $fp = readfile($this->df_filename);
                                 return $fp;


        }

//End program and clean the data

function endExcute()
{

  clearstatcache();


  $this->df_dirpath            = "";
  $this->df_contenttype        = "";
  $this->df_contentdisposition = "attachment";
  $this->df_filename           = "";
  $this->df_size               = 0;
  $this->df_filefermisition    = 0;

  unset($this->df_dirpath, $this->df_contenttype, $this->df_contentdisposition, $this->df_filename, $this->df_size, $this->df_filefermisition);

}

}

?>
