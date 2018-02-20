<?php
namespace ArunasDilius;

class DataGrid
{
    protected $config;
    protected $models;
    protected $r_params;

    public function __construct($models, $config, $r_params = [])
    {
        if (!isset($config['columns']))
            throw new \Exception('Columns configuration not defined');
        $this->config = $config;
        $this->models = $models;
        $this->r_params = $r_params;
    }

    public function getModels()
    {
        return $this->models;
    }

    public function getColumns()
    {
        return $this->config;
    }

    public function renderTable()
    {
        $header = [];
        foreach ($this->config['columns'] as $key => $columnConfig) {
            if (is_string($columnConfig)) {
                $value = ucfirst($columnConfig);
            } elseif (isset($columnConfig['title'])) {
                $value = ucfirst($columnConfig['title']);
            } else {
                $value = ucfirst($key);
            }
            if (isset($columnConfig['sortable']) && $columnConfig['sortable'] == true) {
                if (request()->get('sort_order') == 'asc'
                )
                    $sort_order = 'desc';
                else
                    $sort_order = 'asc';
                $value = '<a href="?' . http_build_query(request()->except(['sort', 'sort_order']) + ['sort' => $key, 'sort_order' => $sort_order]) . '">' . $value . '</a>';
            }
            $header[] = '<th>' . $value . '</th>';
        }
        $body = [];
        foreach ($this->models as $model) {
            $row = [];
            foreach ($this->config['columns'] as $key => $columnConfig) {
                if (is_string($columnConfig)) {
                    $value = $model->$columnConfig;
                } elseif (isset($columnConfig['value']) &&
                    is_object($columnConfig['value']) &&
                    ($columnConfig['value'] instanceof \Closure)
                ) {
                    $value = $columnConfig['value']($model);
                } else {
                    $value = $model->$key;
                }
                $cell_class = null;
                if (isset($columnConfig['cell_class'])) {
                    if (is_object($columnConfig['cell_class']) &&
                        ($columnConfig['cell_class'] instanceof \Closure)
                    ) {
                        $cell_class = $columnConfig['cell_class']($model);
                    } else {
                        $cell_class = $columnConfig['cell_class'];
                    }
                }
                $row[] = '<td' . ($cell_class ? ' class="' . $cell_class . '"' : '') . '>' . $value . '</td>';
            }
            $body[] = '<tr ' . (isset($this->config['row_class']) ? ' class="' . $this->config['row_class']($model) . '"' : '') . '>' . implode('', $row) . '</tr>';
        }
        $table = '<table class="m-05rem-bottom"><thead><tr>' . implode('', $header) . '</tr></thead><tbody>' . implode('', $body) . '</tbody></table>';
        return $table;
    }
}