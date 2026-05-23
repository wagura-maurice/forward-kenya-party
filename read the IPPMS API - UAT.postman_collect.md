read the IPPMS API - UAT.postman_collection.json file and make a service class for IPPMS tooling that i will need to interact with 

Login - {{baseUrl}}/api/Auth/Login?useCookies=false

curl --location 'http://api-ippms.orpp.or.ke/api/Auth/Login?useCookies=false' \
--header 'Content-Type: application/json' \
--data-raw '{
  "username": "wagura465@gmail.com",
  "password": "FKP@Kenya872"
}'

Membership Status - {{baseUrl}}/api/Membership/Status

curl --location 'http://api-ippms.orpp.or.ke/api/Membership/Status' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6IndhZ3VyYTQ2NUBnbWFpbC5jb20iLCJmaXJzdE5hbWUiOiJNQVVSSUNFIFdBR1VSQSIsImxhc3ROYW1lIjoiV0FJVEhBS0EiLCJlbWFpbCI6IndhZ3VyYTQ2NUBnbWFpbC5jb20iLCJ1c2VySWQiOiIwMTljNDIxNS0yYjE1LTcyZTctODJiMy03ZDk4ZmFmN2VkZmMiLCJyb2xlIjoidXNlciIsInBhcnR5LWlkIjoiOTI1NjhlZGUtYmQzOS0zY2I0LWE2YzgtYWI5MjE3YjA0ZjdiIiwicGFydHktY29kZSI6Ijg3MiIsImV4cCI6MTc3ODc5MTkxMCwiaXNzIjoiT1JQUCIsImF1ZCI6Imh0dHBzOi8vYXBpLWlwcG1zLm9ycHAub3Iua2UvIn0.0xRD2JpkEBDrAp3L1h4XDiJs1iUZvT1dHa8tlKun9O4' \
--data '{
  "documentNo": "186328015",
  "documentType": 1
}'

Membershio Details - {{baseUrl}}/api/Membership/Detail

curl --location 'http://api-ippms.orpp.or.ke/api/Membership/Detail' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6IndhZ3VyYTQ2NUBnbWFpbC5jb20iLCJmaXJzdE5hbWUiOiJNQVVSSUNFIFdBR1VSQSIsImxhc3ROYW1lIjoiV0FJVEhBS0EiLCJlbWFpbCI6IndhZ3VyYTQ2NUBnbWFpbC5jb20iLCJ1c2VySWQiOiIwMTljNDIxNS0yYjE1LTcyZTctODJiMy03ZDk4ZmFmN2VkZmMiLCJyb2xlIjoidXNlciIsInBhcnR5LWlkIjoiOTI1NjhlZGUtYmQzOS0zY2I0LWE2YzgtYWI5MjE3YjA0ZjdiIiwicGFydHktY29kZSI6Ijg3MiIsImV4cCI6MTc3ODc5MTkxMCwiaXNzIjoiT1JQUCIsImF1ZCI6Imh0dHBzOi8vYXBpLWlwcG1zLm9ycHAub3Iua2UvIn0.0xRD2JpkEBDrAp3L1h4XDiJs1iUZvT1dHa8tlKun9O4' \
--data '{
  "documentNo": "186328015",
  "documentType": 1
}'

Membership ConfirmationCode - {{baseUrl}}/api/Membership/ConfirmationCode

curl --location 'http://api-ippms.orpp.or.ke/api/Membership/ConfirmationCode' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6IndhZ3VyYTQ2NUBnbWFpbC5jb20iLCJmaXJzdE5hbWUiOiJNQVVSSUNFIFdBR1VSQSIsImxhc3ROYW1lIjoiV0FJVEhBS0EiLCJlbWFpbCI6IndhZ3VyYTQ2NUBnbWFpbC5jb20iLCJ1c2VySWQiOiIwMTljNDIxNS0yYjE1LTcyZTctODJiMy03ZDk4ZmFmN2VkZmMiLCJyb2xlIjoidXNlciIsInBhcnR5LWlkIjoiOTI1NjhlZGUtYmQzOS0zY2I0LWE2YzgtYWI5MjE3YjA0ZjdiIiwicGFydHktY29kZSI6Ijg3MiIsImV4cCI6MTc3ODc5MTkxMCwiaXNzIjoiT1JQUCIsImF1ZCI6Imh0dHBzOi8vYXBpLWlwcG1zLm9ycHAub3Iua2UvIn0.0xRD2JpkEBDrAp3L1h4XDiJs1iUZvT1dHa8tlKun9O4' \
--data '{
  "documentNo": "186328015",
  "documentType": 1,
  "phoneNumber": "+254718837808",
  "firstName": "Baraka"
}'

Membership Register - {{baseUrl}}/api/Membership/Register

curl --location 'http://api-ippms.orpp.or.ke/api/Membership/Register' \
--header 'Content-Type: application/json' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6IndhZ3VyYTQ2NUBnbWFpbC5jb20iLCJmaXJzdE5hbWUiOiJNQVVSSUNFIFdBR1VSQSIsImxhc3ROYW1lIjoiV0FJVEhBS0EiLCJlbWFpbCI6IndhZ3VyYTQ2NUBnbWFpbC5jb20iLCJ1c2VySWQiOiIwMTljNDIxNS0yYjE1LTcyZTctODJiMy03ZDk4ZmFmN2VkZmMiLCJyb2xlIjoidXNlciIsInBhcnR5LWlkIjoiOTI1NjhlZGUtYmQzOS0zY2I0LWE2YzgtYWI5MjE3YjA0ZjdiIiwicGFydHktY29kZSI6Ijg3MiIsImV4cCI6MTc3ODc4NDU2NCwiaXNzIjoiT1JQUCIsImF1ZCI6Imh0dHBzOi8vYXBpLWlwcG1zLm9ycHAub3Iua2UvIn0.An-U1nBsTr9RY_hu-t1Rt0N3k__m2eQ71Ah8To3ELzg' \
--data '{
  "registrationId": "d90434f2-91a5-402c-b53b-c7592f6fa267",
  "confirmationCode": "RXHL7",
  "partyCode": "872",
  "birthDate": "1996-01-03",
  "sex": "F",
  "regDate": "2026-05-14",
  "countyCode": "019",
  "constituencyCode": "099",
  "wardCode": "0495",
  "pwd": false,
  "membershipNo": "FKP-077004"
}'

Membership Counties - {{baseUrl}}/api/Membership/Counties

curl --location 'http://api-ippms.orpp.or.ke/api/Membership/Counties' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6IndhZ3VyYTQ2NUBnbWFpbC5jb20iLCJmaXJzdE5hbWUiOiJNQVVSSUNFIFdBR1VSQSIsImxhc3ROYW1lIjoiV0FJVEhBS0EiLCJlbWFpbCI6IndhZ3VyYTQ2NUBnbWFpbC5jb20iLCJ1c2VySWQiOiIwMTljNDIxNS0yYjE1LTcyZTctODJiMy03ZDk4ZmFmN2VkZmMiLCJyb2xlIjoidXNlciIsInBhcnR5LWlkIjoiOTI1NjhlZGUtYmQzOS0zY2I0LWE2YzgtYWI5MjE3YjA0ZjdiIiwicGFydHktY29kZSI6Ijg3MiIsImV4cCI6MTc3ODc5MTkxMCwiaXNzIjoiT1JQUCIsImF1ZCI6Imh0dHBzOi8vYXBpLWlwcG1zLm9ycHAub3Iua2UvIn0.0xRD2JpkEBDrAp3L1h4XDiJs1iUZvT1dHa8tlKun9O4'

Membership Constituencies - {{baseUrl}}/api/Membership/Constituencies/:countyCode

curl --location 'http://api-ippms.orpp.or.ke/api/Membership/Constituencies/047' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6IndhZ3VyYTQ2NUBnbWFpbC5jb20iLCJmaXJzdE5hbWUiOiJNQVVSSUNFIFdBR1VSQSIsImxhc3ROYW1lIjoiV0FJVEhBS0EiLCJlbWFpbCI6IndhZ3VyYTQ2NUBnbWFpbC5jb20iLCJ1c2VySWQiOiIwMTljNDIxNS0yYjE1LTcyZTctODJiMy03ZDk4ZmFmN2VkZmMiLCJyb2xlIjoidXNlciIsInBhcnR5LWlkIjoiOTI1NjhlZGUtYmQzOS0zY2I0LWE2YzgtYWI5MjE3YjA0ZjdiIiwicGFydHktY29kZSI6Ijg3MiIsImV4cCI6MTc3ODc5MTkxMCwiaXNzIjoiT1JQUCIsImF1ZCI6Imh0dHBzOi8vYXBpLWlwcG1zLm9ycHAub3Iua2UvIn0.0xRD2JpkEBDrAp3L1h4XDiJs1iUZvT1dHa8tlKun9O4'

Membership Wards - {{baseUrl}}/api/Membership/Wards/:constCode

curl --location 'http://api-ippms.orpp.or.ke/api/Membership/Wards/280' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6IndhZ3VyYTQ2NUBnbWFpbC5jb20iLCJmaXJzdE5hbWUiOiJNQVVSSUNFIFdBR1VSQSIsImxhc3ROYW1lIjoiV0FJVEhBS0EiLCJlbWFpbCI6IndhZ3VyYTQ2NUBnbWFpbC5jb20iLCJ1c2VySWQiOiIwMTljNDIxNS0yYjE1LTcyZTctODJiMy03ZDk4ZmFmN2VkZmMiLCJyb2xlIjoidXNlciIsInBhcnR5LWlkIjoiOTI1NjhlZGUtYmQzOS0zY2I0LWE2YzgtYWI5MjE3YjA0ZjdiIiwicGFydHktY29kZSI6Ijg3MiIsImV4cCI6MTc3ODc5MTkxMCwiaXNzIjoiT1JQUCIsImF1ZCI6Imh0dHBzOi8vYXBpLWlwcG1zLm9ycHAub3Iua2UvIn0.0xRD2JpkEBDrAp3L1h4XDiJs1iUZvT1dHa8tlKun9O4'

i nee you to create me a service clas foe inter ceting with this IPPMS api endpoints with respect to whats document in the 