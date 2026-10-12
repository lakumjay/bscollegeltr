<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courses;
use App\Models\FeeStructure;
use App\Models\SpecialCategory;
use App\Models\SpecialCategoryFeeHead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpecialCategoryController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('fee_management.index', array_filter([
            'course_id' => $request->query('course_id'),
        ]))->with('info', 'Special-category pricing has been retired. Use the payment discount option instead.');
    }

    public function store(Request $request)
    {
        return redirect()->route('fee_management.index', array_filter([
            'course_id' => $request->input('course_id'),
        ]))->with('info', 'Special-category pricing has been retired. Use the payment discount option instead.');
    }
}
