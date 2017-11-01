
# Core Api Functionalites

#### TO DO
- characters
- add/update/delete anime
- add/update/delete episodes
- add/update/delete videos

#### Anime list
```
/anime
```
#### Anime list based on conditions
```
/anime?type=tv
/anime?premiered=Fall 2016
```
etc..

#### Get an anime by its id
```
/anime/52
```

#### Get an anime with its slug
```
/anime?slug=bleach
```

#### Append ?episode=all to get all episodes in an anime
```
/anime/52?episodes=all
```

#### Search for an anime based on a keyword
```
/anime/search/watashi
```

#### Get an episode by its id
```
/episode/113625
```

#### Get an episode by its slug
```
/episode?slug=bleach-episode-1
```

#### Get a list of anime with a certain genre
```
/genre/1
```

#### Get a list of anime with multiple genres
```
/genre?include=1,3,5
```
# anime-api
