<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\{ Project, Customer, ProjectSupplier };

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public static function index()
    {
        return Project::with('customer')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customer_name = $request->customer_name;

        $customer = Customer::whereName($customer_name)->first();

        if (!$customer)
        {
            $customer = new Customer;
        }

        $customer->name = $customer_name;
        $customer->contact = $request->contact;
        $customer->fax = $request->fax;
        $customer->email = $request->email;

        $customer->save();

        $customer_id = $customer->id;

        $project = new Project;

        $project->name = $request->name;
        $project->start_on = $request->start_on;
        $project->end_on = $request->end_on;
        $project->cost = $request->cost;
        $project->customer_id = $customer_id;
        $project->address = $request->address;
        $project->remarks = $request->remarks;

        $project->save();

        foreach ($request->supplierIds as $supplierId)
        {
            $ps = new ProjectSupplier;

            $ps->project_id = $project->id;
            $ps->supplier_id = $supplierId;

            $ps->save();
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Project::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return Project::with('customer')->find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customer_name = $request->customer_name;

        $project = Project::find($id);

        $project->name = $request->name;
        $project->start_on = $request->start_on;
        $project->end_on = $request->end_on;
        $project->cost = $request->cost;
        $project->address = $request->address;
        $project->remarks = $request->remarks;

        $customer = Customer::whereName($customer_name)->first();

        if (!$customer)
        {
            $customer = new Customer;
        }

        $customer->name = $customer_name;
        $customer->contact = $request->contact;
        $customer->fax = $request->fax;
        $customer->email = $request->email;

        $customer->save();

        $project->customer_id = $customer->id;

        $project->save();

        ProjectSupplier::whereProjectId($project->id)->delete();

        foreach ($request->supplierIds as $supplierId)
        {
            $ps = new ProjectSupplier;

            $ps->project_id = $project->id;
            $ps->supplier_id = $supplierId;

            $ps->save();
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Project::destroy($id);
    }

    public static function jsProjectSupplierIdDict()
    {
        $projects = Project::with('suppliers')->get();

        $project_suppliers = [];

        foreach ($projects as $project)
        {
            $supplierIds = [];

            foreach ($project->suppliers as $supplier)
            {
                array_push($supplierIds, $supplier->id);
            }

            array_push($project_suppliers, $project->id . ':' . '[' . join(',', $supplierIds) . ']');
        }

        return '{' . join(',', $project_suppliers) . '}';
    }
}
