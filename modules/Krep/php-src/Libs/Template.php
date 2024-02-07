<?php

namespace KWCMS\modules\Krep\Libs;


/**
 * Class Template
 * basic working with templates in files
 * automatically cache them for later usage
 *
 * @package KWCMS\modules\Krep\Libs
 */
class Template extends \kalanis\kw_templates\ATemplate
{
    protected $currentOne = '';

    protected static $tpls = [
        "default" => '{NOTHING}',
        "head" => '<!DOCTYPE html>
<html><head>
<title>{TITLE}</title>
<meta http-equiv="content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="proxy.css">
</head><body>
<h1>{NAME}</h1>
{CONTENT}
</body></html>',
        "index" => '<h3>{SMALL_LINK}</h3>
        <ul>{LINKS}</ul><h3>{WARNING}</h3><p>{HINT}</p>
',
        "index_links" => '<li><a href="{ADDR}">{NAME}</a> <a href="{ADDR}#dwn" title="{DOWN}">&nbsp;v&nbsp;</a></li>',
        "discus" => '<h2>{NAME}</h2><p><a name="up" href="#dwn">{DOWN} v</a><br> {ARCHIVE} {CONTINUE}</p><hr>
<div class="topics">{CONTENT}</div>
<p><a name="dwn" href="#up">&nbsp;^&nbsp;{UP}</a> {CONTINUE}</p>
',
//// "discus_thema"=>'<b>{NAME}</b> &nbsp;<a href="{LINK}" name="d_{NUM}" title="{READ}"> &gt;&gt; </a>  &nbsp; <a href="{LINK}#dwn" title="{DOWN}">&nbsp;&#x25be;&nbsp;</a>&nbsp;<small> &nbsp; ({DATE})</small> {ADD}<hr>'."\r\n",
//        "discus_thema" => '<b>{NAME}</b> &nbsp;<a href="{LINK}" name="d_{NUM}" title="{READ}"> &gt;&gt; </a>  &nbsp; <a href="{LINK}#dwn" title="{DOWN}">&nbsp;v&nbsp;</a>&nbsp;<small> &nbsp; ({DATE})</small> {ADD}<hr>' . "\r\n",
//        "discus_thema_spec" => '<b><i>{NAME}</i></b><hr>' . "\r\n",
        "discus_add" => ' <a href="{ADD}{LINK}" title="{ADD_ONE}">&nbsp;+&nbsp;</a>',
        "discus_topic" => '<p><a name="POST{NUM}" id="d_{NUM}"></a><b>{USERNAME}</b>#{POSTID}@{DATETIME}&gt;&gt;{CONTENT}</p><hr>
',
        "discus_more" => '<a href="{LINK}">{READ}</a>',
        "discus_read" => '{TOPIC_NO}<u>{POSTID}</u> {FROM_USER} <b>{USERNAME}</b>.<br><i>{DATETIME} </i><br>{CONTENT}<br>
  <a href="{LINK}">{BACK}</a>
',
        "discus_archive" => '<a href="{LINK}">{ARCHIVE}</a><br>',
        "discus_rest" => '<a href="{LINK}" title="{ADD_ONE}">{ADD_MESS}</a>',
        "add_reply" => '<h2>{THEMA_NAME} - {TOPIC_NAME} - {ADD_POST}</h2><form method="post"><!-- smerovacky -->
<!-- prispevek --><div class="nebrat"><label><b>{POST}</b><br>{MESSAGE_INPUT}</label><br><label><b>{USER}</b>{USER_INPUT}</label><br><label><b>{PASS}</b>{PASS_INPUT}</label><br>{MAIL_TEMPLATE}</div><div class="radsi"><label><b>{WEB}*</b>{LINK_INPUT}<br></div>
<!-- jen pro potreby proxy -->
{SUBMIT_INPUT}</form><p></p>
<p>{HINTS}<br></p>{CONTINUE}',
        "add_reply_mail" => '<label><b>{MAIL}</b>{EMAIL_INPUT}</label><br>',
        "" => '',
        "continue" => '<p><a href="{LINK_DISCUS}">{BACK_TO_DISCUS}</a> <a href="{LINK_THEMES}">{BACK_TO_THEMES}</a> <a href="{LINK_BEGIN}">{BACK_TO_BEGIN}</a></p>',
        "sent" => '<h3>{HIGH}</h3><p>{DESC}<br> {CONTINUE}</p>',
        "nosent" => '<h3>{PROBLEM}</h3><p> {ERRNUM} ({ERRSTR}) <br /> {NOT_SENT}<br />{CONTINUE}</p>',
        "ban" => '<h3>{PROBLEM}</h3><p>{CONTINUE}</p><br />',
//        "" => '',
    ];

    public function __construct(string $contentKey)
    {
        static::$tpls["discus_thema"] = '<p>{NAME} &nbsp;<a href="{LINK}" name="d_{NUM}" id="d_{NUM}" title="{READ}"> &gt;&gt; </a>  &nbsp; <a href="{LINK}#dwn" title="{DOWN}">&nbsp;v&nbsp;</a>&nbsp;<small> &nbsp; ({DATE})</small> {ADD}</p><hr>' . "\r\n";
        static::$tpls["discus_thema_spec"] = '<h4>{NAME}</h4><hr>' . "\r\n";

        $this->currentOne = $contentKey;
        parent::__construct();
    }

    protected function loadTemplate(): string
    {
        return isset(static::$tpls[$this->currentOne]) ? static::$tpls[$this->currentOne] : $this->currentOne ;
    }

    protected function fillInputs(): void
    {
        // nothing need here
    }
}
