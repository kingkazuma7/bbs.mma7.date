@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>選手管理</h1>
                <a href="{{ url('/admin/fighter/create') }}" class="btn btn-primary">新規追加</a>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($fighters->isEmpty())
                <div class="alert alert-info" role="alert">
                    選手がまだ登録されていません。
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>画像</th>
                                <th>選手名</th>
                                <th>投票数</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fighters as $fighter)
                                <tr>
                                    <td>{{ $fighter->id }}</td>
                                    <td>
                                        @if ($fighter->image_path)
                                            <img src="{{ $fighter->image_path }}" alt="{{ $fighter->name }}" style="width: 50px; height: 50px; object-fit: contain;">
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $fighter->name }}</td>
                                    <td>{{ $fighter->votes()->count() }}</td>
                                    <td>
                                        <a href="{{ url('/admin/fighters/' . $fighter->id . '/edit') }}" class="btn btn-sm btn-warning">編集</a>
                                        <form action="{{ url('/admin/fighters/' . $fighter->id) }}" method="POST" class="d-inline" onsubmit="return confirm('削除してもよろしいですか？');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">削除</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $fighters->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
