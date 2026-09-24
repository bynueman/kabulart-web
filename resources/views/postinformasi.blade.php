<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/post.css">
    <title>add informasi</title>
</head>
<body style="background: lightgray;">
    <div class="container">
        <div>
            <h3 class="text">INFORMASI</h3>
        </div>
        <div class="crbody">
            <a href="/homeadmin" class="btnh">HOME</a>
            <a href="{{ route('postsinformasi.create') }}" class="btnsus">TAMBAH POST</a>
        </div>
        <table> 
            <thead>
                <tr>
                    <th scope="col">AKSI</th>
                    <th scope="col">FOTO</th>
                    <th class="ftotbel" scope="col">DESKRIPSI</th>
                    
                </tr>
            </thead>
            <tbody>
              @forelse ($posts as $post)  
                <tr>
                <td class="text-center">
                        <form onsubmit="return confirm('Apakah Anda Yakin ?');" action="{{ route('postsinformasi.destroy', $post->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button" style="vertical-align:middle" ><span>DELETE</span></button>
                        </form>
                    </td>
                    <td>
                        <img src="{{ asset('/storage/postsimg/'.$post->image) }}" class="rounded" alt="image" style="width: 100px; height: 100px;" >
                    </td>
                    <td>{{ $post->deskripsi }}</td>
                   
                </tr>
              @empty
                    <div class="danger">
                        Data Post belum Tersedia.
                    </div>
              @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>