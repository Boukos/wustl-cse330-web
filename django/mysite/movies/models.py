from __future__ import unicode_literals

from django.db import models
from django.utils.encoding import python_2_unicode_compatible

# Create your models here.
@python_2_unicode_compatible
class Movie(models.Model):
	movie_title = models.CharField(max_length=100)
	pub_date = models.DateTimeField('date published')
	avgrating = models.IntegerField(default=0)
	director = models.CharField(max_length=100)
	movie_cover = models.CharField(max_length=400)
	movie_genre = models.CharField(max_length=100)
	def __str__(self):
		return self.movie_title

@python_2_unicode_compatible
class Rating(models.Model):
	movie = models.ForeignKey(Movie, on_delete=models.CASCADE)
	rate_user = models.CharField(max_length=100)
	intrating = models.IntegerField(default=5) #
	comment = models.CharField(max_length=200)
	def __str__(self):
		return self.rate_user+'RATING'+self.movie.movie_title
	def getRating(self):
		return self.intrating
