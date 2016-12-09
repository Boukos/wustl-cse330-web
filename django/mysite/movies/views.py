from django.shortcuts import render
from django.shortcuts import get_object_or_404

from django.http import HttpResponse
from django.http import HttpResponseRedirect
from django.http import Http404
from django.urls import reverse

from django.template import loader

from django.db.models import Avg
from django.db.models import Max

from .models import Movie, Rating

# Create your views here.
def index(request):
	# limited_movie_list
	ob = request.POST.get('orderby', 'id')
	limited_movie_list = Movie.objects.order_by(ob)
	# hottest
	max_rating = Movie.objects.all().aggregate(Max('avgrating')).values()[0]
	max_movie_list = Movie.objects.filter(avgrating=max_rating) #[:3]
	# personal
	recommend = []
	if request.user.is_authenticated():
		user_name = request.user.username
		user_ratings = Rating.objects.filter(rate_user=user_name)
		if user_ratings:
			max_user_rating = user_ratings.aggregate(Max('intrating')).values()[0]
			user_genre = user_ratings.filter(intrating=max_user_rating)[0].movie.movie_genre
			recommend = Movie.objects.filter(movie_genre=user_genre).order_by('avgrating')[:1]

	context = {
		'limited_movie_list': limited_movie_list,
		'max_movie_list': max_movie_list,
		'personal_movie_list':recommend,
	}
	return render(request, 'movies/index.html', context)
	
def detail(request, movie_id):
	movie = get_object_or_404(Movie, pk=movie_id)
	return render(request, 'movies/detail.html', {'movie': movie,})

def rate(request, movie_id):
	movie = get_object_or_404(Movie, pk=movie_id)
	try:
		rating = movie.rating_set.get(rate_user=request.user.username)
	except Rating.DoesNotExist:
		movie.rating_set.create(
		rate_user=request.user.get_username(),
		intrating=request.POST['rating'],
		comment=request.POST['comment'],)
	else:
		rating.intrating=request.POST['rating']
		rating.comment=request.POST['comment']
		rating.save()
	movie.avgrating = movie.rating_set.aggregate(Avg('intrating')).values()[0]
	movie.save()
	
	return HttpResponseRedirect(reverse('movies:detail', args=(movie.id,)))

def search(request):
	search_title = request.POST.get('searchcontent', '')
	try:
		movie = Movie.objects.get(movie_title=search_title)
		#get_object_or_404(Movie, movie_title=search_title)
	except Movie.DoesNotExist:
		raise Http404("Movie does not exist")
	
	return render(request, 'movies/detail.html', {'movie': movie,})
	