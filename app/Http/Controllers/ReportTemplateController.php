<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportTemplateController extends Controller
{
    public function index(){
        $data = dcru_dt('report_templates', 'dtables');
        return view('setting.report.index', $data);
    }
    public function create(){
        $model = new \App\ReportTemplate;
        $mode = 'create';

        return view('setting.report.form', compact('model', 'mode', 'types'));
    }
    public function duplicate($id){
        $model = \App\ReportTemplate::findOrFail($id);
        $mode = 'create';

        return view('setting.report.form', compact('model', 'mode', 'types'));
    }
    public function view($id){
        $model = \App\ReportTemplate::findOrFail($id);
        $mode = 'create';

        return view('setting.report.view', compact('model'));
    }

    public function save(Request $request){
        // dd($request->all());
        $data = $request->all();
        $data['company_id'] = company('id');
        $data['created_by'] = user('id');
        if(!empty($request->is_default)){
            \App\ReportTemplate::where('report_name', $request->report_name)->update(['is_default'=>0]);
        }
        \App\ReportTemplate::create($data);
        return redirect()->route('report_templates.index')->with('success', 'New template created');
    }

    public function edit($id){
        $model = \App\ReportTemplate::findOrFail($id);
        $mode = 'edit';

        return view('setting.report.form', compact('model', 'mode', 'types'));
    }
    public function update(Request $request, $id){
        $data = $request->all();
        $data['company_id'] = company('id');
        $data['created_by'] = user('id');
        $model = \App\ReportTemplate::findOrFail($id);
        if(!empty($request->is_default)){
            if($request->is_default=='1'){
                \App\ReportTemplate::where('report_name', $model->report_name)->where('is_default', 1)->update(['is_default'=>0]);
                $model->is_default = 1;
            }
        }

        $model->report_name = $request->report_name;
        $model->template_name = $request->template_name;
        $model->template_content = $request->template_content;
        $model->save();
        return redirect()->route('report_templates.index')->with('success', 'New template created');
    }

    public function delete($id){
        $model = \App\ReportTemplate::findOrFail($id);
        $model->delete();
        return redirect()->route('report_templates.index')->with('success', 'Template deleted');
    }
}
