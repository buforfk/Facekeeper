<?php
/**
 * hax4 Coding Standard.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    bu <bu@hax4.in>
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_Standards_CodingStandard', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_CodingStandard not found');
}

/**
 * MyStandard Coding Standard.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    bu <bu@hax4.in>
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_Standards_HAX4_HAX4CodingStandard extends PHP_CodeSniffer_Standards_CodingStandard
{
    /**
     * Return a list of external sniffs to include with this standard.
     *
     * The MyStandard coding standard uses some generic sniffs, and
     * the entire PEAR coding standard.
     *
     * @return array
     */
    public function getIncludedSniffs()
    {
	return array(
		'PEAR'
	       );
    
    }//end getIncludedSniffs()

   
    /**
     * Returneturn a list of external sniffs to exclude from this standard.
     *
     * The MyStandard coding standard uses all PEAR sniffs except one.
     *
     * @return array
     */
    public function getExcludedSniffs()
    {
    return array(
            'PEAR/Sniffs/ControlStructures/ControlSignatureSniff.php',
            'PEAR/Sniffs/NamingConventions/ValidClassNameSniff.php',
            'PEAR/Sniffs/NamingConventions/ValidVariableNameSniff.php',
            'PEAR/Sniffs/NamingConventions/ValidFunctionNameSniff.php'
    
           );

    }//end getExcludedSniffs() 
}//end class
