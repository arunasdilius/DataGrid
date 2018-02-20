<?php
$dataGrid = new DataGrid($models, [
        'columns' => [
            'id' => ['sortable' => true, 'value' => function ($model) {
                return '<a href="' . route('create', ['id' => $model->id]) . '">' . $model->id . '</a>';
            }],
            'full_name' => ['sortable' => true, 'title' => 'Full name'],
            'email' => ['sortable' => true],
            'created_at' => ['sortable' => true, 'title' => 'Created at'],
            'last_seen_at' => ['sortable' => true],
            'auth' => ['value' => function ($model) {
                if ($model->twitter_id)
                    return 'TWITTER';
                elseif ($model->google_user_id)
                    return 'GOOGLE';
                elseif ($model->facebook_id)
                    return 'FACEBOOK';
                else
                    return 'MANUAL';
            }],
            'groups' => ['cell_class' => 'bold', 'value' => function ($model) {
                return $model->groupes()->count();
            }],
            'templates' => ['value' => function ($model) {
                $a = $model->activities()->withTrashed()->where('type', Activity::TYPE_TEMPLATE)->where('demo', 0)->get();
                $html = '<span>' . $a->count() . '</span> / ';
                $c = $a->where('quickfire_id', '>', 0)->count();
                $html .= '<span class="color-quickfire ' . ($c ? 'baseline line-height-15rem text-125rem bold' : '') . '">' . $c . '</span> / ';
                $c = $a->where('discuss_id', '>', 0)->count();
                $html .= '<span class="color-discuss ' . ($c ? 'baseline line-height-15rem text-125rem bold' : '') . '">' . $c . '</span> / ';
                $c = $a->where('team_up_id', '>', 0)->count();
                $html .= '<span class="color-team_up ' . ($c ? 'baseline line-height-15rem text-125rem bold' : '') . '">' . $c . '</span> / ';
                $c = $a->where('clip_id', '>', 0)->count();
                $html .= '<span class="color-clip ' . ($c ? 'baseline line-height-15rem text-125rem bold' : '') . '">' . $c . '</span>';
                return $html;
            }],
        ]
    ]
);

$dataGrid->renderTable();