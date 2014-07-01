<?php
/*---------------------------------------------------------------------------
 * @Project: Alto CMS
 * @Project URI: http://altocms.com
 * @Description: Advanced Community Engine
 * @Copyright: Alto CMS Team
 * @License: GNU GPL v2 & MIT
 *----------------------------------------------------------------------------
 */

/**
 * @package plugin Categories
 * @since   0.9.5
 */

class PluginCategories_ModuleCategory_MapperCategory extends MapperORM {

    /**
     * @param $aBlogId
     *
     * @return array
     */
    public function GetCategoriesByBlogId($aBlogId) {

        $sql = "
            SELECT
              cr.blog_id, c.*
              FROM ?_category_rel AS cr
              INNER JOIN ?_category AS c ON c.category_id=cr.category_id
            WHERE
              cr.blog_id IN (?a)
        ";
        if ($aRows = $this->oDb->select($sql, $aBlogId)) {
            $aResult = array();
            $aCategories = array();
            foreach ($aRows as $aRow) {
                $aResult[$aRow['blog_id']] = array();
                if (isset($aCategories[$aRow['category_id']])) {
                    $oCategory = $aCategories[$aRow['category_id']];
                    $oCategory->setBlogId($aRow['blog_id']);
                } else {
                    $oCategory = Engine::GetEntity('Category', $aRow);
                }
                $aResult[$aRow['blog_id']][$oCategory->getId()] = $oCategory;
            }
            return $aResult;
        }
        return array();
    }

    /**
     * @param string $sType
     * @param array  $aFilter
     *
     * @return array
     */
    public function GetCategoryTopicsId($sType, $aFilter) {

        if (!isset($aFilter['category_id'])) {
            $sql = "
                SELECT DISTINCT category_id
                FROM ?_category_rel
            ";
            $aFilter['category_id'] = $this->oDb->selectCol($sql);
        }
        if (isset($aFilter['period']) && $aFilter['period']) {
            $sDate = F::DateTimeSub('P' . ($aFilter['period'] + 1) . 'D');
            $sPeriod = "((CASE WHEN topic_date_show IS NULL THEN topic_date_add ELSE topic_date_show END)>'" . $sDate . "')";
        } else {
            $sPeriod = '(1=1)';
        }
        $sBlogTypes = '';
        foreach($aFilter['blog_type'] as $sBlogType) {
            if ($sBlogTypes) {
                $sBlogTypes .= ',';
            }
            $sBlogTypes .= "'" . $sBlogType . "'";
        }

        if (isset($aFilter['rating'])) {
            $sRating = 't.topic_rating>=' . intval($aFilter['rating']);
        } else {
            $sRating = '1=1';
        }

        $iLimit = intval($aFilter['limit']);
        $sSub = "
                SELECT
                    cr.category_id AS ARRAY_KEY_1,
                    topic_id AS ARRAY_KEY_2,
                    topic_id
                FROM ?_topic AS t
                INNER JOIN ?_blog AS b ON b.blog_id=t.blog_id AND b.blog_type IN(?a:blog_type)
                INNER JOIN ?_category_rel AS cr ON cr.blog_id=b.blog_id
                WHERE
                      t.topic_publish=1
                      AND ?s:rating
                      AND cr.category_id=?:category_id
                      AND ?s:period
        ";
        if ($sType == 'new') {
            $sSub .= "
                ORDER BY CASE WHEN topic_date_show IS NULL THEN topic_date_add ELSE topic_date_show END DESC
            ";
        } else {
            $sSub .= "
                ORDER BY topic_rating DESC, CASE WHEN topic_date_show IS NULL THEN topic_date_add ELSE topic_date_show END DESC
            ";
        }
        $sSub .= "
                LIMIT ?:limit
        ";

        $sql = "";
        foreach ($aFilter['category_id'] as $iCategoryId) {
            if ($sql) {
                $sql .= " UNION ALL ";
            }
            $sql .= "("
                . str_replace(
                    array('?:category_id', '?a:blog_type', '?:limit', '?s:period', '?s:rating'),
                    array(intval($iCategoryId), $sBlogTypes, $iLimit, $sPeriod, $sRating),
                    $sSub)
                . ")";
        }
        $aResult = $this->oDb->select($sql);
        if ($aResult) {
            foreach ($aResult as $iCategoryId=>$aTopicsId) {
                $aTopicsId = array_keys($aTopicsId);
                foreach($aTopicsId as $iTopicId) {
                    $aResult[$iCategoryId][$iTopicId] = $iTopicId;
                }
            }
        }
        return ($aResult !== false) ? $aResult : array();
    }

}

// EOF