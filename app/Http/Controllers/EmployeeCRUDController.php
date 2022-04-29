<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Employee;
use App\Payroll;
use App\DailyTimeRecord;
use App\DTRabsent;
use App\DtrPassword;
use App\DTRHoursshifting;
use App\Cashadvance;
use App\Dummypayroll;

class EmployeeCRUDController extends Controller
{

    public function __construct()

    {

        $this->middleware('auth');

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // User role permission start here
        if(!auth()->user()->canAccess('EmployeeCRUDController.index')){
            return view('permission-denied');
        }
        // User role permission end here


        $employees = Employee::orderBy('employee_name','ASC')->paginate(5);
        //return view('ItemCRUD.index',compact('items'))
        return view('list-employees.index',compact('employees'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // User role permission start here
        if(!auth()->user()->canAccess('EmployeeCRUDController.add')){
            return view('permission-denied');
        }
        // User role permission end here


        return view('list-employees.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*$this->validate($request, [
            'employee_name' => 'required',
            'tin_no' => 'required',
            'address' => 'required',
            'birthday' => 'required',
            'sex' => 'required',
            'status' => 'required',
            'dependents' => 'required',
            'daily_rate' => 'required',
            'monthly_rate' => 'required',
            'overtime_rate' => 'required',
            'late_rate' => 'required',
        ]);*/

        Employee::create($request->all());
        return redirect()->route('list-employees.index')
        ->with('success','Employee created successfully');
    }

    public function addToPayroll(Request $request)
    {
        foreach ($request->select as $id){ 
            if(!empty($request->select)){
                        $result= Dummypayroll::updateOrCreate(array('e_id' => $id));
            }else{}
        }
            return redirect()->route('payroll.index')
                ->with('success','Employee added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // User role permission start here
        if(!auth()->user()->canAccess('EmployeeCRUDController.show')){
            return view('permission-denied');
        }
        // User role permission end here


        $employee = Employee::find($id);
        return view('list-employees.show',compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // User role permission start here
        if(!auth()->user()->canAccess('EmployeeCRUDController.edit')){
            return view('permission-denied');
        }
        // User role permission end here


        $employee = Employee::find($id);
        return view('list-employees.edit',compact('employee'));
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
        /*$this->validate($request, [
            'employee_name' => 'required',
            'tin_no' => 'required',
            'address' => 'required',
            'birthday' => 'required',
            'sex' => 'required',
            'status' => 'required',
            'dependents' => 'required',
            'salary_method' => 'required',
            'daily_rate' => 'required',
            'monthly_rate' => 'required',
            'overtime_rate' => 'required',
            'absent_rate' => 'required',
            'late_rate' => 'required',
        ]);*/

        Employee::find($id)->update($request->all());
        return redirect()->route('list-employees.index')
                        ->with('success','Employee updated successfully');
    }

    // public function getDestroy($id=null)
    // {
    //     $employee = Employee::findOrFail($id);
    //     $employee->delete();
    //             return redirect()->route('list-employees.index')
    //                     ->with('success','Employee deleted successfully');
    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // User role permission start here
        if(!auth()->user()->canAccess('EmployeeCRUDController.delete')){
            return view('permission-denied');
        }
        // User role permission end here


        Employee::find($id)->delete();
        DailyTimeRecord::where('e_id', $id)->delete();
        DTRabsent::where('e_id', $id)->delete();
        DtrPassword::where('e_id', $id)->delete();
        DTRHoursshifting::where('e_id', $id)->delete();
        Cashadvance::where('e_id', $id)->delete();
        Payroll::where('e_id', $id)->delete();
        return redirect()->route('list-employees.index')
        ->with('success','Employee deleted successfully');
    }
}