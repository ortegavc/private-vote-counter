<?php
/**
 * Miscellaneous utility methods.
 */
final class Utils {
  
  private function __construct() {
  }
  
  public static function getUsername() {
    if(!SessionValidator::isLoguedIn()) {
      Utils::redirect('login');
    }
    return $_SESSION['username'];
  }

    /**
     * Generate link.
     * @param string $page target page
     * @param array $params page parameters
     */
    public static function createLink($page, array $params = array()) {
        $params = array_merge(array('page' => $page), $params);
        // TODO add support for Apache's module rewrite
        return 'index.php?' .http_build_query($params);
    }

    /**
     * Format date.
     * @param DateTime $date date to be formatted
     * @return string formatted date
     */
    public static function formatDate(DateTime $date = null) {
        if ($date === null) {
            return '';
        }
        return $date->format('m/d/Y');
    }

    /**
     * Format date and time.
     * @param DateTime $date date to be formatted
     * @return string formatted date and time
     */
    public static function formatDateTime(DateTime $date = null) {
        if ($date === null) {
            return '';
        }
        return $date->format('m/d/Y H:i');
    }

    /**
     * Redirect to the given page.
     * @param type $page target page
     * @param array $params page parameters
     */
    public static function redirect($page, array $params = array()) {
        header('Location: ' . self::createLink($page, $params));
        die();
    }

    /**
     * Get value of the URL param.
     * @return string parameter value
     * @throws NotFoundException if the param is not found in the URL
     */
    public static function getUrlParam($name) {
        if (!array_key_exists($name, $_GET)) {
            throw new NotFoundException('URL parameter "' . $name . '" not found.');
        }
        return $_GET[$name];
    }

    /**
     * Get {@link Acta} by the identifier 'id' found in the URL.
     * @return Acta {@link Acta} instance
     * @throws NotFoundException if the param or {@link Acta} instance is not found
     */
    public static function getActaByGetId() {
        $id = null;
        try {
            $id = self::getUrlParam('id');
        } catch (Exception $ex) {
            throw new NotFoundException('No ACTA identifier provided.');
        }
        if (!is_numeric($id)) {
            throw new NotFoundException('Invalid ACTA identifier provided.');
        }
        $dao = new ActaDao();
        $acta = $dao->findById($id);
        if ($acta === null) {
            throw new NotFoundException('Unknown ACTA identifier provided.');
        }
        return $acta;
    }    

    /**
     * Capitalize the first letter of the given string
     * @param string $string string to be capitalized
     * @return string capitalized string
     */
    public static function capitalize($string) {
        return ucfirst(mb_strtolower($string));
    }

    /**
     * Escape the given string
     * @param string $string string to be escaped
     * @return string escaped string
     */
    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES);
    }
  
}
