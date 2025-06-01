host name = "http://127.0.0.1:8000/"


1 :- register api {
    url = "http://127.0.0.1:8000/api/register" , 
    method = "POST",
    perameters in body (form-data) = "name , email , password , password_confirmation"
}


2 :- login api {
    url = "http://127.0.0.1:8000/api/login" , 
    method = "POST",
    perameters in body (form-data) = " email , password "
}

3 :- logout api{
    url = "http://127.0.0.1:8000/api/logout",
    method = "POST",
}

======================== User =================================

1 :- get all users {
    url = "http://127.0.0.1:8000/api/user/index" ,
    method = "GET",
    headers = {"Authorization" : "Bearer <token>"},
}
2 :- show users {
    url = "http://127.0.0.1:8000/api/user/show"
    method = "POST",
    headers = {"Authorization" : "Bearer <token>"},
    parameters in body (form-data) = "id"
}
3 :- update users {
    url = "http://127.0.0.1:8000/api/user/update"
    method = "POST",
    headers = {"Authorization" : "Bearer <token>"},
    parameters in body (form-data) = "id","name","email","phone","address","village_name","gender"
}
4 :- delete users {
    url = "http://127.0.0.1:8000/api/user/delete"
    method = "POST",
    headers = {"Authorization" : "Bearer <token>"},
    parameters in body (form-data) = "id"
}
======================== villages =================================


1 :- get all villages {
    url = "http://127.0.0.1:8000/api/village/index" ,
    method = "GET",
    headers = {"Authorization" : "Bearer <token>"},
}
2 :- add village {
    url = "http://127.0.0.1:8000/api/store/village"
    method = "POST",
    headers = {"Authorization" : "Bearer <token>"},
    parameters in body (form-data) = "name"
}

