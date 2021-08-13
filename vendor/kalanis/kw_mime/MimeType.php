<?php

namespace kalanis\kw_mime;


use kalanis\kw_paths\Stuff;


/**
 * Class MimeType
 * @package kalanis\kw_mime
 */
class MimeType
{
    protected $localAtFirst = false;
    protected $mimeTypes = [
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'rtf' => 'application/x-rtf',
        'wml' => 'text/vnd.wap.wml',
        'wmls' => 'text/vnd.wap.wmlscript',
        // images
        'bmp' => 'image/bmp',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'png' => 'image/png',
        'pcx' => 'image/x-pcx',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',
        // archives
        'arj' => 'application/x-arj-compressed',
        'bz2' => 'application/x-bzip2',
        'cab' => 'application/vnd.ms-cab-compressed',
        'lha' => 'application/x-lha',
        'lzh' => 'application/x-lzh',
        'lzx' => 'application/x-lzx',
        'msi' => 'application/x-msdownload',
        'rar' => 'application/x-rar-compressed',
        'z' => 'application/x-compress',
        'zip' => 'application/zip',
        '7z' => 'application/x-7z-compressed',
        // installers
        'deb' => 'application/x-debian-package',
        'rpm' => 'application/x-rpm',
        'tar' => 'application/x-tar',
        'tgz' => 'application/x-gzip',
        // audio/video
        'aif' => 'audio/aiff',
        'asf' => 'video/x-ms-asf',
        'asx' => 'application/x-ms-asf',
        'au' => 'audio/basic',
        'avi' => 'video/avi',
        'flv' => 'video/x-flv',
        'kar' => 'audio/midi',
        'm3u' => 'audio/x-mpegurl',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'mkv' => 'video/x-matroska',
        'mov' => 'video/quicktime',
        'mp3' => 'audio/mpeg',
        'mp4' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'ogg' => 'video/ogg',
        'qt' => 'video/quicktime',
        'ra' => 'audio/x-pn-realaudio',
        'ram' => 'audio/x-pn-realaudio',
        'snd' => 'audio/basic',
        'swf' => 'application/x-shockwave-flash',
        'webm' => 'video/webm',
        'wma' => 'audio/x-ms-wma',
        'wmv' => 'video/x-ms-wmv',
        'wav' => 'audio/wav',
        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',
        // office
        'doc' => 'application/msword',
        'xla' => 'application/x-msexcel',
        'xls' => 'application/vnd.ms-excel',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'wp' => 'application/wordperfect',
        'wp6' => 'application/wordperfect',
        'wri' => 'application/mswrite',
        // tech
        'dwg' => 'application/acad',
        'hlp' => 'application/help',
        'skp' => 'application/x-koan',
        // programming and system
        'c' => 'text/plain',
        'cpp' => 'text/x-c',
        'class' => 'application/java',
        'conf' => 'text/plain',
        'crt' => 'application/x-x509-ca-cert',
        'c++' => 'text/plain',
        'inf' => 'application/inf',
        'java' => 'text/x-java-source',
        'list' => 'text/plain',
        'lst' => 'text/plain',
        'meta' => 'text/metadata',
        'mime' => 'www/mime',
        'pas' => 'text/pascal',
        'py' => 'text/x-script.python',
        'sh' => 'application/x-sh',
        'exe' => 'application/octet-stream',
        // custom
        'phpkg' => 'application/x-php-package', // php package for KWCMS - base64 + serialize
        'phg' => 'application/x-php-compress', // php package for KWCMS - plain serialize
    ];

    public function __construct(bool $localAtFirst = false)
    {
        $this->localAtFirst = $localAtFirst;
    }

    public function mimeByPath(string $path): string
    {
        if ($this->localAtFirst) {
            return $this->mimeByExt(Stuff::fileExt($path));
        }
        if (function_exists('mime_content_type')) {
            return mime_content_type($path);
        }
        if (method_exists('\finfo', 'buffer')) {
            $fi = new \finfo(FILEINFO_MIME); # file mimetype
            $mm = $fi->buffer($path);
            unset($fi);
            return $mm;
        }
        return $this->mimeByExt(Stuff::fileExt($path));
    }

    public function mimeByExt(string $ext): string
    {
        $ext = strtolower($ext);
        return isset($this->mimeTypes[$ext]) ? $this->mimeTypes[$ext] : 'application/octet-stream';
    }
}
