
zip: clean
	@zip -r "crowdsec-wp.zip" . -x "./tests/**" -x ".git/**" -x "./github/**" > /dev/null && echo "./crowdsec-wp.zip generated !"

clean:
	@rm "crowdsec-wp.zip" || echo "crowdsec-wp.zip doesn't exist."