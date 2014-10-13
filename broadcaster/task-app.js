var app = require('express')()
    , server = require('http').createServer(app)
    , io = require('socket.io').listen(server)
    , users = [];

server.listen(8080);

app.get('/', function (req, res) {
    res.sendFile(__dirname + '/index.html');
});

io.sockets.on('connection', function (socket) {
//    socket.emit('news', { hello:'world' });
//    socket.broadcast.emit('new-user',{user: 'new user'});

    socket.on('new-phase', function (data) {
        socket.broadcast.emit('new-phase', data);
    });
    socket.on('remove-phase', function (data) {
        socket.broadcast.emit('remove-phase', data);
    });
    socket.on('move-phase', function (data) {
        socket.broadcast.emit('move-phase', data);
    });
    socket.on('new-user', function (data) {
        socket.broadcast.emit('new-user', data);
    });
    socket.on('new-task', function (data) {
        socket.broadcast.emit('new-task', data);
    });
    socket.on('update-task-info', function (data) {
        socket.broadcast.emit('update-task-info', data);
    });
    socket.on('complete-task', function (data) {
        socket.broadcast.emit('complete-task', data);
    });
    socket.on('move-task', function (data) {
        socket.broadcast.emit('move-task', data);
    });

    socket.on('disconnect', function () {
        users.splice(users.indexOf(socket.nickname)+1, 1);
        socket.broadcast.emit('user-anouncement', socket.nickname+' has just left.');
        socket.emit('update-users', users);
    });
});

var editing_terms = {};
var usersConnect = {};

var pubsweet = io
  .of('/pubsweet')
  .on('connection', function (socket) {
        socket.on('editing-terms',function(){
            socket.emit('editing-terms',editing_terms);
        });
        socket.on('new-term-editing',function(data){
            if(editing_terms[socket.id]){// if not is editing
                socket.broadcast.emit('remove-term-editing',editing_terms[socket.id]);
                delete editing_terms[socket.id];
            }
            socket.broadcast.emit('new-term-editing',data);
            editing_terms[socket.id] = data;
        });

        socket.on('editing-books',function(data){
            socket.emit('users-editing-book',usersConnect);
            socket.broadcast.emit('new-user-editing-book',data);
            usersConnect[socket.id]= data;
        });
//
//        socket.on('new-user-editing-books',function(data){
//
//        });

        socket.on('remove-term-editing',function(data){
            socket.broadcast.emit('remove-term-editing',data);
            if(editing_terms[socket.id])
                delete editing_terms[socket.id];
        });
        socket.on('disconnect',function(){
            var data = editing_terms[socket.id];
            socket.broadcast.emit('remove-term-editing',data);
            delete editing_terms[socket.id];
            data = usersConnect[socket.id];
            socket.broadcast.emit('remove-user-editing',data);
            delete usersConnect[socket.id];
        });

    var events = ['new-book','new-section','delete-section','new-chapter','delete-chapter',
        'move-section','move-chapter','new-term','updating-term','delete-term',
        'delete-chapter-status','add-chapter-status','update-status-chapter',
        'updateTitleChapter','updateTitleSection',
        'add-like','remove-like','new-review','plus-approve','new-message'
    ];
    simpleBroadcaster(events, socket);

  });





/**
 * For every item set a listener and an emiter with the same event name
 * @param array events
 * @param socket
 */
function simpleBroadcaster(events, socket){
    for(var i = 0; i<events.length; i++){
        socket.on(events[i], (function(event){
            return function(data){
                console.log(event);
                socket.broadcast.emit(event, data);
            }

        })(events[i]));
    }
}

var currentlyEditing = [];
var pubsweetAdvanced = io
    .of('/pubsweet')
    .on('connection', function (socket) {

        socket.on('lock-wysi',function(data){
            socket.broadcast.emit('lock-wysi', data);
            currentlyEditing.push(data);
        });
        socket.on('unlock-wysi', function(data){
            socket.broadcast.emit('unlock-wysi', data);
            var length = currentlyEditing.length;
            for(var i = 0; i<length; i++){
                if(currentlyEditing[i]==null){
                    delete currentlyEditing[i];
                    continue;
                }
                if(currentlyEditing[i]!=undefined && currentlyEditing[i].chapter_id==data.chapter_id){
                    delete currentlyEditing[i];
                }
            }
        });
        socket.on('list-current-editing', function(data){
            return data;
        });

    });

app.get('/pubsweetbackend/editing-sections', function (req, res) {
    var length = currentlyEditing.length,
        chapters = [];
    for(var i = 0; i<length; i++){
        if(currentlyEditing[i]==undefined){
            continue;
        }
        if(chapters.indexOf(currentlyEditing[i])==-1){
            chapters.push(currentlyEditing[i].chapter_id);
        }else{
            delete currentlyEditing[i];
        }
    }
//    res.setHeader('Content-Type', 'application/json');
//    res.end(JSON.stringify(currentlyEditing));
    res.jsonp(currentlyEditing);
});
