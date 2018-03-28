<?php


class CTodo
{
  var $m_text;


  function __construct( $text )
  {
    $this->setText( $text );
  }

  function setText( $newText )
  {
    $this->m_text = $newText;
  }
  function getText()
  {
    return $this->m_text;
  }
}


?>