<pre><?php
/*
Snippet Name: Install WordPress core from a zip file with auto download
Snippet URI: https://github.com/szepeviktor/wordpress-plugin-construction
Description: Unzip WordPress instead of uploading it file-by-file
Version: 0.2
License: The MIT License (MIT)
Author: Viktor Szépe
Author URI: http://www.online1.hu/webdesign/
*/


// path to WordPress core zip
$ZIP = './wordpress.zip';

// report every error
error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

// check for the ZIP extension
if ( ! class_exists( 'ZipArchive' ) )
    exit( 'No ZipArchive class.' );

$zip = new ZipArchive;

if ( true !== $zip->open( $ZIP ) ) {
    print 'ZIP open error (' . $ZIP . '), downloading latest release...<br>';
    // downloading latest.zip
    file_put_contents( $ZIP, file_get_contents( 'https://wordpress.org/latest.zip' ) );
    if ( true !== $zip->open( $ZIP ) )
        exit( 'Could not download latest release.' );
}

// extract the zip file in place
if ( ! $zip->extractTo( '.' ) )
    exit( 'Extraction failed, maybe write permission problems.' );

$zip->close();

if ( ! unlink( $ZIP ) )
    exit( $ZIP . ' could not be deleted.' );

print '<strong>OK. Please delete ' . __FILE__;
