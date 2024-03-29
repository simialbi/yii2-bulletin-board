<?php
/**
 * @package yii2-bulletin-board
 * @author Simon Karlen <simi.albi@outlook.com>
 */

namespace simialbi\yii2\bulletin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class SearchBoard extends Board
{
    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'created_by'], 'integer'],
            [['title', 'description', 'icon'], 'string'],
            [['status', 'is_public'], 'boolean']
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function scenarios(): array
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Board::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'title' => SORT_ASC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'is_public' => $this->is_public,
            'created_by' => $this->created_by
        ]);
        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
}
