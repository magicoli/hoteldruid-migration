<?php

function hdm_get_option( $option, $default = false ) {
  $options = get_option('hoteldruid-migration');
  $value = (isset($options[$option])) ? $options[$option] : $default;
  // $value = rwmb_meta( $option, ['object_type' => 'setting'], 'hoteldruid-migration' );
  // if($value === NULL) return $default;
  return $value;
}

function hdm_update_option( $option, $value, $autoload = null, $args=[] ) {
  rwmb_set_meta( 'hoteldruid-migration', $option, $value, $args );
}

function node2array($node)
{
  $array = false;
  if ($node->hasAttributes())
  {
    foreach ($node->attributes as $attr)
    {
      $array[$attr->nodeName] = $attr->nodeValue;
    }
  }

  if ($node->hasChildNodes())
  {
    if ($node->childNodes->length == 1)
    {
      $array[$node->firstChild->nodeName] = $node->firstChild->nodeValue;
    }
    else
    {
      foreach ($node->childNodes as $childNode)
      {
        if ($childNode->nodeType != XML_TEXT_NODE)
        {
          $array[$childNode->nodeName][] = node2array($childNode);
        }
      }
    }
  }

  return $array;
}

function flatten_array($data) {
  if(!isset($data['colonnetabella'][0]['nomecolonna'])) return $data;
  if(!isset($data['righetabella'][0]['riga'])) return [];
  $rows = array();
  $keys = array_map('join', $data['colonnetabella'][0]['nomecolonna']);
  $values = $data['righetabella'][0]['riga'];
  foreach($values as $key=>$value) {
    $row = array_combine($keys, $value['cmp']);
    if(is_array($row)) {
      $row = array_map('join_if_array', $row);
      $rows[] = array_map('join_if_array', $row);
    }
  }
  return $rows;
}

function join_if_array($value) {
  if(is_array($value)) return join($value);
  return $value;
}
