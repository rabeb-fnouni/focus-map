<?php

namespace App\Http\Controllers;
use App\Models\Goal;
use App\Models\Journal;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $journals = Journal::all();
        return view('journal.index', compact('journals'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $goals = Goal::all();
        return view('journal.create', compact('goals'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'goal_id' => 'required|exists:goals,id',
        ]);

        $journal = new Journal();
        $journal->title = $request->input('title');
        $journal->content = $request->input('content');
        $journal->goal_id = $request->input('goal_id');
        $journal->save();

        return redirect()->route('journal.index')->with('success', 'Journal entry created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $journal = Journal::findOrFail($id);
        return view('journal.show', compact('journal'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $journal = Journal::findOrFail($id);
        return view('journal.edit', compact('journal'));
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
        $journal = Journal::findOrFail($id);
        $journal->content = $request->input('content');
        $journal->goal_id = $request->input('goal_id');
        $journal->save();

        return redirect()->route('journal.index')->with('success', 'Journal entry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $journal = Journal::findOrFail($id);
        $journal->delete();

        return redirect()->route('journal.index')->with([
            'message' => 'Journal deleted successfully',
            'alert-type' => 'success',
        ]);
    }
}
