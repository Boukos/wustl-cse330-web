from django.conf.urls import url

from . import views

app_name = 'movies'
urlpatterns = [
	url(r'^$', views.index, name='index'),
	url(r'^(?P<movie_id>[0-9]+)/$', views.detail, name='detail'),
	url(r'^(?P<movie_id>[0-9]+)/rate/$', views.rate, name='rate'),
	url(r'^search$', views.search, name='search'),
]