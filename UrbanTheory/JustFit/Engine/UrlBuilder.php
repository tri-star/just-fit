<?php

namespace UrbanTheory\JustFit\Engine;


use UrbanTheory\JustFit\Model\Condition;

/**
 * ZOZOTOWNの検索用クエリを作成するオブジェクト
 */
class UrlBuilder {
    
    const HOST_SEARCH_URL = 'http://zozo.jp/_search/search_result.html';
    
    public function __construct() {
    }
    
    /**
     * ZOZOTOWN検索用のURLを返す。
     * @param Condition $condition 検索条件
     * @return string 検索用URL
     */
    public function buildHostSearchUrl(Condition $condition) {
        
        $params = array(
                        'p_maid'   => 1, //不明
                        'p_tycid'  => $condition->get('product_category', 101),
                        'p_tyid'   => $condition->get('clothing_category', 2001),
                        'p_cutyid' => $condition->get('gender', 1),
                        'p_stype'  => $condition->get('sale_type'),
                        'p_ptype'  => $condition->get('price_type', 0),
                        'dcolor'   => 1,
                        'dstk'     => $condition->get('stock'),
                        'dord'     => $condition->get('order', 32),
                        'free_price' => 1,
                        'p_pris'   => $condition->get('price_min', 0),
                        'p_prie'   => $condition->get('price_max', 5000),
                        'pno'      => $condition->get('page', 1),
                        'search'   => '',
        );
        
        $descConditions = array();
        
        if($condition->hasValue('sleeve_styles')) {
            $descConditions = array_merge($descConditions, $this->getSleeveStyleValues($condition->get('sleeve_styles')));
        }
        if($condition->hasValue('print_styles')) {
            $descConditions = array_merge($descConditions, $this->getPrintStyleValues($condition->get('print_styles')));
        }
        if($condition->hasValue('neck_styles')) {
            $descConditions = array_merge($descConditions, $this->getNeckStyleValues($condition->get('neck_styles')));
        }
        
        if(count($descConditions) > 0) {
            $params['p_gttagid'] = $descConditions;
        }
        return self::HOST_SEARCH_URL . '?' . http_build_query($params);
    }
    
    
    /**
     * Conditionオブジェクトが持つsleeve_stylesの値をZOZOTOWN検索用の値に変換して返す。
     * @param array $input Conditionオブジェクトのsleeve_stylesに含まれるデータ(Condition::SLEEVE_STYLE_XXX)
     */
    protected function getSleeveStyleValues($input) {
        
        if(!is_array($input)) {
            return array();
        }
        
        $result = array();
        foreach($input as $style) {
            switch($style) {
                case Condition::SLEEVE_STYLE_SHORT:  $result[] = '5493_21158'; break;
                case Condition::SLEEVE_STYLE_LONG:  $result[] = '5493_21159'; break;
            }
            continue;
        }
        return $result;
    }
    
    /**
     * Conditionオブジェクが持つprint_stylesの値をZOZOTOWN検索用の値に変換して返す。
     * @param array $input Conditionオブジェクトのsleeve_stylesに含まれるデータ(Condition::PRINT_STYLE_XXX)
     */
    protected function getPrintStyleValues($input) {
        if(!is_array($input)) {
            return array();
        }
        
        $result = array();
        foreach($input as $style) {
            switch($style) {
                case Condition::PRINT_STYLE_PLAIN:     $result[] = '5497_21172'; break;
                case Condition::PRINT_STYLE_BORDER:    $result[] = '5497_21173'; break;
                case Condition::PRINT_STYLE_STRIPE:    $result[] = '5497_21175'; break;
                case Condition::PRINT_STYLE_CHECK:     $result[] = '5497_21176'; break;
                case Condition::PRINT_STYLE_ONE_POINT: $result[] = '5497_21181'; break;
                case Condition::PRINT_STYLE_PRINT:     $result[] = '5497_21182'; break;
                case Condition::PRINT_STYLE_MISC:      $result[] = '5497_21183'; break;
            }
            continue;
        }
        return $result;
    }
    
    
    /**
     * Conditionオブジェクが持つneck_stylesの値をZOZOTOWN検索用の値に変換して返す。
     * @param array $input Conditionオブジェクトのneck_stylesに含まれるデータ(Condition::NECK_STYLE_XXX)
     */
    protected function getNeckStyleValues($input) {
        if(!is_array($input)) {
            return array();
        }
        
        $result = array();
        foreach($input as $style) {
            switch($style) {
                case Condition::NECK_STYLE_U:     $result[] = '5498_21184'; break;
                case Condition::NECK_STYLE_V:     $result[] = '5498_21185'; break;
                case Condition::NECK_STYLE_HENRY: $result[] = '5498_21186'; break;
                case Condition::NECK_STYLE_MISC:  $result[] = '5498_21189'; break;
            }
            continue;
        }
        return $result;
    }
    
}
