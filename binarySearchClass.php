<?php

class BinarySearch
{
  public static $start_symbol = "\n"; //Символ, после которого начинается ключ
  public static $end_symbol = "\t"; //Символ, перед которым ключ заканчивается
  
  protected static $fp; //Указатель на открытый файл для избежания многократных применений fopen()

  // открывает файл и начинает поиск. Возвращает найденное значение или null
  public static function find( $filename, $token )
  {
    if( !self::$fp )
    {
      if( !is_readable( $filename ) )
      {
        throw new Exception( 'File cannot be read: ' . $filename );
      }
      self::$fp = fopen( $filename, 'r' );
      if( !self::$fp )
      {
        throw new Exception( 'File cannot be opened for reading: ' . $filename );
      }
    }
    return self::binary( 0, filesize( $filename ), $token );
  }

  //Само применение бинарного поиска
  protected static function binary( $Lb, $Ub, $token )
  {
    while( true )
    {
      $M = $Lb + round( ($Ub-$Lb)/2 );
      $candidate = self::parseAt( $M, self::$start_symbol, self::$end_symbol );
      $cmp = strcmp( $token, $candidate );
      if ( $cmp < 0 )
      {
        $Ub = $M - 1;
      }
      elseif ( $cmp > 0 )
      {
        $Lb = $M + 1;
      }
      else
      {
        return self::parseAt( $M + strlen($candidate), self::$end_symbol, self::$start_symbol );
      }
      if ( $Lb > $Ub )
      {
        return null;
      }
    }
  }

  //Сначала ищет начальный символ (по умолчанию \n), затем проходит каждый символ ключа и до конечного символа включительно
  protected static function parseAt( $pos, $start_symbol="\n", $end_symbol="\t" )
  {
    while( $pos )
    {
      self::seek( $pos );
      if( $start_symbol == self::getSymbol() )
      {
        break; 
      }
      $pos--;
    }
    
    if( $pos > 0 )
    {
      $pos++; //пропуск символа новой строки, если не в начале файла
    }

    $token = '';
    while( !feof( self::$fp ) )
    {
      self::seek( $pos );
      $symbol = self::getSymbol();
      if( $end_symbol == $symbol )
      {
        break; //перебор ключа завершен
      }
      $token .= $symbol;
      $pos++;
    }
      
    return $token;
  }

  //применение fseek() с поддержкой исключения
  protected static function seek( $pos )
  {
    if( fseek( self::$fp, $pos ) < 0 )
    {
      throw new Exception( 'Cannot fseek in the file. fseek() = ' . fseek( self::$fp, $pos ) . ', pos = ' . $pos );
    }
   return true;
  }

  //возвращает текущий символ в открытом файле 
  protected static function getSymbol()
  {
    return fgetc( self::$fp );
  }

}