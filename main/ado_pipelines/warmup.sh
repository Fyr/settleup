# Define the website URL
url=$1

# Loop until the curl command succeeds
while true; do
# Use curl with -s for silent, -o for output to /dev/null, and -w for write-out the http code
http_code=$(curl -s -o /dev/null -w "%{http_code}" "$url")

# Check if the http code is 200
if [ "$http_code" == "200" ]; then
# Print a success message and break the loop
echo "The website $url is up and running!"
break
else
# Print a dot and wait for 5 seconds
echo -n "."
sleep 5
fi
done