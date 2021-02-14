<?php
  /** Sanitization functions */

  /**
   * An alias of `ShiftCodesTKDatabase::clean_str()`
   * 
   * @param string $str The string to be escaped.
   * @return string Returns the escaped string.
   */
  function clean_sql ($str) {
    GLOBAL $_mysqli;
    
    $clean = $str;
    $clean = collapseWhitespace($clean);
    $clean = $_mysqli->con->real_escape_string($clean);

    return $clean;
  }
  /**
   * Convert special characters to HTML entities
   *
   * @param string $str The string to sanitize
   * @return string The sanitized string
   */
  function clean_all_html ($str) {
    return htmlspecialchars($str, ENT_COMPAT|ENT_HTML5|ENT_QUOTES);
  }
  /**
   * Convert HTML entities back to their equivalent characters
   * 
   * @param string $str The string to decode
   * @return string The decoded string
   */
  function decode_html ($str) {
    return htmlspecialchars_decode($str, ENT_COMPAT|ENT_HTML5|ENT_QUOTES);
  }
  /**
   * Remove non-whitelisted HTML & PHP tags from a string
   *
   * @param string $str The string to sanitize
   * @param string $whitelist Whitelisted HTML & PHP tags
   * @return string The sanitized string
   */
  function clean_html ($str, $whitelist = '<div><span><p><strong><b><em><i>') {
    return strip_tags($str, $whitelist);
  }
  /**
   * Sanitize a string for use in a URL
   *
   * @param string $str The string to sanitize
   * @return string The sanitized string
   */
  function clean_url ($str) {
    return urlencode(clean_all_html($str));
  }
  /**
   * Decode a string used in a URL
   *
   * @param string $str The string to decode
   * @return string The dirty string
   */
  function decode_url ($str) {
    return urldecode(decode_html($str));
  }
  /**
   * Sanitize a string for use as an ID
   * 
   * @param string $str The string to sanitize
   * @return string The sanitized string
   */
  function clean_id ($str) {
    return clean_url(strtolower(preg_replace('/\s+/', '_', $str)));
  }
?>