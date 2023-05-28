<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="row p-0 m-0">
      <div class="col-3" id="UserContainer">
        <div class="row user-container-row">
          <div class="col-12 user-details px-4 py-3" style="
          background-image: url('{{ asset('img/user-header-bg.jpg') }}')">
            <img src="{{ asset('img/Profile-Pic.jpg') }}" class="profile-picture mb-3" alt="Profile Picture">
            <h3 class="user-name mb-2">{{ Auth::user()->name }}</h3>
            <a class="nav-link logout" href="{{ route('logout') }}"
            onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">
            Logout
            </a>
    
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
          </div>
          <div class="col-12 pt-3">
            <h2>ADD TASK</h2>
            <form class="add-task-container" method="POST" action="/tasks">
              @csrf
              <div class="form-group mb-2">
                  <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" required>
              </div>
              <div class="form-group mb-2">
                  <textarea class="form-control" id="description" name="description" placeholder="Enter description" required></textarea>
              </div>
              <div class="form-group mb-2">
                  <select class="form-control" id="category" name="category_id" required>
                    <option value="" selected disabled>Select Category</option>
                      @foreach ($categories as $category)
                          <option value="{{ $category->id }}">{{ $category->name }}</option>
                      @endforeach
                  </select>
              </div>
              <button type="submit" class="btn btn-primary">Add Task</button>
          </form>
          </div>
        </div>
        
      </div>
      <div class="col-12 col-md-9" id="taskContainer">
        <div class="row">
          <div class="col-12 pt-3 ps-4 Todo-Task-Header" style="
          background-image: url('{{ asset('img/task-header-bg.png') }}')">
            <button class="transparent-button mb-5 pb-4" type="button" id="toggleBtn">
              <span class="fas fa-bars"></span>
            </button>
            <h2 class="tasks-header"> To Do Tasks</h2>
            <p>{{ date('l j F') }}</p>
          </div>
          <div class="col-12 todo-task-container">
              @forelse ($tasks as $task)
              @if ($task->user_id == auth()->user()->id)
                <div class="my-3" id="accordion">
                  <div class="accordion-item" style="border:1px solid black">
                      <div class="row accordion-header py-2" id="heading{{ $task->id }}">
                        <div class="col-4 ps-4">
                          {{ $task->title }}
                        </div>
                        <div class="col-4 text-center">
                          {{ $task->completed ? 'Completed' : 'On Progress' }}
                        </div><div class="col-4 expand-accordion pe-4 d-flex justify-content-end">
                          <button class="accordion-button d-flex justify-content-end" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $task->id }}" aria-expanded="true" aria-controls="collapse{{ $task->id }}" onclick="toggleAccordion(this)">
                            <i class="fas fa-plus"></i>
                          </button>
                        </div>
                        
                      </div>
                      <div id="collapse{{ $task->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $task->id }}" data-bs-parent="#accordion">
                          <div class="accordion-body">
                            <div class="table-responsive">
                              <table class="table">
                                  <thead>
                                      <tr>
                                          <th>Title</th>
                                          <th class="task-description-header">Description</th>
                                          <th>Category</th>
                                          <th>Action</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      <tr>
                                          <td>{{ $task->title }}</td>
                                          <td class="task-description-content">{{ $task->description }}</td>
                                          <td>{{ $task->category->name }}</td>
                                          <td class="action-task-container d-flex text-center">
                                              <form method="POST" action="{{ route('tasks.update', $task->id) }}">
                                                  @csrf
                                                  @method('PUT')
                                                  <input type="hidden" name="completed" value="{{ $task->completed ? 0 : 1 }}">
                                                  <button type="submit" class="btn btn-primary">
                                                    @if ($task->completed)
                                                        <i class="fas fa-undo"></i>
                                                    @else
                                                        <i class="fas fa-check"></i>
                                                    @endif
                                                  </button>   
                                              </form>
                                              @if ($task->completed)
                                                  <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" style="display: inline-block;">
                                                      @csrf
                                                      @method('DELETE')
                                                      <button type="submit" class="btn btn-danger ms-1">
                                                        <i class="fas fa-trash"></i>
                                                    </button></form>
                                              @endif
                                          </td>
                                      </tr>
                                  </tbody>
                              </table>
                            </div>
                          </div>
                      </div>
                  </div>  
                </div>
                @endif
              @empty
              <div class="text-center no-task">
                <i class="fas fa-check"></i> <br> No Task
              </div>
              @endforelse
                
              <div class="task-overlay"></div>
          </div>
        </div>
      </div>
    </div>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Handle hamburger icon click event
    $('#toggleBtn').click(function() {
    if ($(window).width() > 767) {
        $('#UserContainer').toggleClass('d-none');
        $('#taskContainer').toggleClass('col-md-9 col-12');
        
    }
    else{
      $('#UserContainer').toggleClass('position-absolute');
      if ($('#UserContainer').hasClass('position-absolute')) {
        $('#UserContainer').css({
          'display': 'block',
          'position': 'absolute',
          'top': '0',
          'right': '0',
          'bottom': '0',
          'left': '0',
          'background-color': 'rgba(255, 255, 255, 1)',
          'min-width' : '250px',
          'z-index': '2'
        });
        $(".task-overlay").css({
          'display' : 'block'
        });
      } 
      else {
        $('#UserContainer').css({
          'display': 'none',
          'position': '',
          'top': '',
          'right': '',
          'bottom': '',
          'left': ''
        });
      }
    }
    });
    $(".task-overlay").click(function() {
      $('#UserContainer').toggleClass('position-absolute');
      $('#UserContainer').css({
          'display': 'none',
          'position': '',
          'top': '',
          'right': '',
          'bottom': '',
          'left': ''
        });
      $(".task-overlay").css({
        'display' : 'none'
      });
    });
    function toggleAccordion(button) {
  $(button).find('i').toggleClass('fa-plus fa-minus');
}

</script>
</html>