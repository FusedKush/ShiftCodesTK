<?php
  /** Sanitization functions */

  /**
   * Sanitize an SQL Statement for querying in a database
   * @param string $str The SQL Statement to escape
   * @return string The escaped SQL Statement
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
    return htmlspecialchars($str);
  }
  /**
   * Convert special, non-whitelisted characters to HTML entities
   *
   * @param string $str The string to sanitize
   * @param string $whitelist Whitelisted HTML tags
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
   * Sanitize a string for use as an ID
   * 
   * @param string $str The string to sanitize
   * @return string The sanitized string
   */
  function clean_id ($str) {
    return clean_url(strtolower(preg_replace('/\s+/', '_', $str)));
  }
?>