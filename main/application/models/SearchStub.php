<?php

class Application_Model_SearchStub
{
    // stub emulates DB result
    private function getSuggestions($query)
    {
        $dataToVerify = [
            'qwerty asdf',
            'uiopasdf cxsdf',
            'ghjklzxc fgg',
            'vbnm fnhjkl',
        ];
        $res = [];
        //$noRes = array('no results');

        foreach ($dataToVerify as $value) {
            $pos = strpos($value, (string) $query);
            if ($pos !== false) {
                array_push($res, $value);
            }
        }

        //        if (empty($res))
        //            $res = $noRes;

        return $res;
    }

    /**
     * data (optional)
     *   - data array, that contains values for callback function when data
     *   is selected.
     * $data = array(
     *  'for callback1', 'for callback1', 'for callback1', 'for callback1'
     * );
     *
     * @param $query
     * @return string
     */
    public function getHints($query)
    {
        $suggestions = $this->getSuggestions($query);

        return $this->prepareValidJsonStructure($query, $suggestions);
    }

    private function prepareValidJsonStructure(
        $query,
        array $suggestions,
        array $data = []
    ) {
        $res = [
            'query' => $query,
            'suggestions' => $suggestions,
            'data' => $data,
        ];

        return json_encode($res, JSON_THROW_ON_ERROR);
    }
}
