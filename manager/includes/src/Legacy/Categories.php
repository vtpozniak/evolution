<?php namespace EvolutionCMS\Legacy;

/**
 * Class to handle the modx-categories
 */
class Categories
{
    /**
     * @var DBAPI
     */
    public $db;
    public $db_tbl = array();
    public $elements = array('templates', 'tmplvars', 'htmlsnippets', 'snippets', 'plugins', 'modules');

    public function __construct()
    {
        $modx = evolutionCMS();

        $this->db = &$modx->db;
        $this->db_tbl['categories'] = $modx->getFullTableName('categories');

        foreach ($this->elements as $element) {
            $this->db_tbl[$element] = $modx->getFullTableName('site_' . $element);
        }
    }


    /**
     * Get all categories
     * @return  array   $categories / array contains all categories
     */
    public function getCategories()
    {
        $categories = $this->getDatabase()->makeArray(
            $this->getDatabase()->select(
                '*',
                $this->db_tbl['categories'],
                '1',
                '`rank`,`category`'
            )
        );

        return empty($categories) ? array() : $categories;
    }

    /**
     * @param string $search
     * @param string $where
     * @return array|bool|object|stdClass
     */
    public function getCategory($search, $where = 'category')
    {
        $category = $this->getDatabase()->getRow(
            $this->getDatabase()->select(
                '*',
                $this->db_tbl['categories'],
                "`" . $where . "` = '" . $this->getDatabase()->escape($search) . "'"
            )
        );

        return $category;
    }

    /**
     * @param string $value
     * @param string $search
     * @param string $where
     * @return bool|int|string
     */
    public function getCategoryValue($value, $search, $where = 'category')
    {
        $_value = $this->getDatabase()->getValue(
            $this->getDatabase()->select(
                '`' . $value . '`',
                $this->db_tbl['categories'],
                "`" . $where . "` = '" . $this->getDatabase()->escape($search) . "'"
            )
        );

        return $_value;
    }

    /**
     * @param int $category_id
     * @param string $element
     * @return array|bool
     */
    public function getAssignedElements($category_id, $element)
    {
        if (in_array($element, $this->elements, true)) {
            $elements = $this->getDatabase()->makeArray(
                $this->getDatabase()->select(
                    '*',
                    $this->db_tbl[$element],
                    "`category` = '" . (int)$category_id . "'"
                )
            );

            // correct the name of templates
            if ($element === 'templates') {
                $_elements_count = count($elements);
                for ($i = 0; $i < $_elements_count; $i++) {
                    $elements[$i]['name'] = $elements[$i]['templatename'];
                }
            }

            return $elements;
        }

        return false;
    }

    /**
     * @param int $category_id
     * @return array
     */
    public function getAllAssignedElements($category_id)
    {
        $elements = array();
        foreach ($this->elements as $element) {
            $elements[$element] = $this->getAssignedElements($category_id, $element);
        }

        return $elements;
    }

    /**
     * @param int $category_id
     * @return bool
     */
    public function deleteCategory($category_id)
    {
        $_update = array('category' => 0);
        foreach ($this->elements as $element) {
            $this->getDatabase()->update(
                $_update,
                $this->db_tbl[$element],
                "`category` = '" . (int)$category_id . "'"
            );
        }

        $this->getDatabase()->delete(
            $this->db_tbl['categories'],
            "`id` = '" . (int)$category_id . "'"
        );

        return $this->getDatabase()->getAffectedRows() === 1;
    }

    /**
     * @param int $category_id
     * @param array $data
     * @return bool
     */
    public function updateCategory($category_id, $data = array())
    {
        if (empty($data) || empty($category_id)) {
            return false;
        }

        $_update = array(
            'category' => $this->getDatabase()->escape($data['category']),
            'rank'     => (int)$data['rank']
        );

        $this->getDatabase()->update(
            $_update,
            $this->db_tbl['categories'],
            "`id` = '" . (int)$category_id . "'"
        );

        if ($this->getDatabase()->getAffectedRows() === 1) {
            return true;
        }

        return false;
    }

    /**
     * @param string $category_name
     * @param int $category_rank
     * @return bool|int|mixed
     */
    public function addCategory($category_name, $category_rank)
    {
        if ($this->isCategoryExists($category_name)) {
            return false;
        }

        $_insert = array(
            'category' => $this->getDatabase()->escape($category_name),
            'rank'     => (int)$category_rank
        );

        $this->getDatabase()->insert(
            $_insert,
            $this->db_tbl['categories']
        );

        if ($this->getDatabase()->getAffectedRows() === 1) {
            return $this->getDatabase()->getInsertId();
        }

        return false;
    }

    /**
     * @param string $category_name
     * @return bool|int|string
     */
    public function isCategoryExists($category_name)
    {
        $category = $this->getDatabase()->escape($category_name);

        $category_id = $this->getDatabase()->getValue(
            $this->getDatabase()->select(
                '`id`',
                $this->db_tbl['categories'],
                "`category` = '" . $category . "'"
            )
        );

        if ($this->getDatabase()->getAffectedRows() === 1) {
            return $category_id;
        }

        return false;
    }
}
