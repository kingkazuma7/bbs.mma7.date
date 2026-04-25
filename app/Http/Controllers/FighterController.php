<?php

namespace App\Http\Controllers;

use App\Models\Fighter;
use Illuminate\Http\Request;

class FighterController extends Controller
{
    private function getImageValidationRules($isRequired = true)
    {
        return [
            'name' => 'required|string|max:255',
            'image' => ($isRequired ? 'required' : 'nullable') . '|image|mimes:jpeg,png,gif,webp|max:5120',
        ];
    }

    private function getImageValidationMessages()
    {
        return [
            'name.required' => '選手名は必須です',
            'name.max' => '選手名は255文字以内でお願いします',
            'image.required' => '画像は必須です',
            'image.image' => '画像ファイルをアップロードしてください',
            'image.mimes' => 'jpeg, png, gif, webp形式のみ対応しています',
            'image.max' => '画像は5MB以内でお願いします',
        ];
    }

    public function index()
    {
        $fighters = Fighter::paginate(10);
        return view('fighter.index', compact('fighters'));
    }

    public function create()
    {
        return view('fighter.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->getImageValidationRules(true),
            $this->getImageValidationMessages()
        );

        $originalName = $request->file('image')->getClientOriginalName();
        $request->file('image')->storeAs('fighters', $originalName, 'public');

        Fighter::create([
            'name' => $validated['name'],
            'image_url' => $originalName,
        ]);

        return redirect('/admin/fighters')->with('success', '選手を追加しました');
    }

    public function edit($id)
    {
        $fighter = Fighter::findOrFail($id);
        return view('fighter.edit', compact('fighter'));
    }

    public function update(Request $request, $id)
    {
        $fighter = Fighter::findOrFail($id);

        $validated = $request->validate(
            $this->getImageValidationRules(false),
            $this->getImageValidationMessages()
        );

        $fighter->name = $validated['name'];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $originalName = $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('fighters', $originalName, 'public');
            $fighter->image_url = $originalName;
        }

        $fighter->save();

        return redirect('/admin/fighters')->with('success', '選手を更新しました');
    }

    public function destroy($id)
    {
        $fighter = Fighter::findOrFail($id);
        $fighter->delete();

        return redirect('/admin/fighters')->with('success', '選手を削除しました');
    }
}
