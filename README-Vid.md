# README-Vid.md

## Notes on PHP Temporal samples

You can walk through all the samples by standing up docker
`docker compose up`
and then in a new tab, runing sample name in an 'exec' like so:
`docker-compose exec app php app.php update`

Each app/src/ sample dir has a README and in it is the cmd to run that sample.

Here's a quick way to walk through all 31
Build the list of samples:

```
# get the list of samples from the README's and transform the string
samples=$(ag -iQu 'php ./app/app.php ' | grep 'php ./app/app.php' | gsed -E 's~\.md:[0-9]+:php ./app/~.md:php ~' | awk -F'md:' '{print "docker-compose exec app "$2}');
# set your iterator
i=1;
```

Run this command over and over again to run the sample, see the output and queue up the next sample (i+1);

```
echo $i:; echo "$samples" | awk "NR==$i" && $(echo "$samples" | awk "NR==$i"); i=$((i+1))
```

If the above fails. You can cmd+. and then `echo $i` to see where you are in the list. If you need to manually change that number `i=7`; do that and run the echo line again.

For the most part you can cut and paste once; hit enter;
Then: up arrow; enter; repeat
