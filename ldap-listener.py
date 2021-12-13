#!/usr/bin/env python3
# 
# Listens for LDAP request byte sequence instead of relying on just DNS lookups
# when checking your assets for the for log4j vulnerability.
#
# Test for vulnerable log4j installations using string ${jndi:ldap://your-server-running-this-listener.net:11389/}
#
# See https://ldap.com/ldapv3-wire-protocol-reference-bind/
#
# The following example demonstrates the encoding for an anonymous simple bind request with a message ID of one and no request controls:
#
# 30 0c -- Begin the LDAPMessage sequence
#    02 01 01 --  The message ID (integer value 1)
#    60 07 -- Begin the bind request protocol op
#       02 01 03 -- The LDAP protocol version (integer value 3)
#       04 00 -- Empty bind DN (0-byte octet string)
#       80 00 -- Empty password (0-byte octet string with type context-specific primitive zero)


import socket

HOST = '0.0.0.0'
PORT = 11389
LDAP_HELLO = b'\x30\x0c\x02\x01\x01\x60\x07\x02\x01\x03\x04\x00\x80\x00'

with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
    s.bind((HOST, PORT))
    s.listen()
    while True:
        conn, addr = s.accept()
        with conn:
            print('Received connection from', addr)
            data = conn.recv(4096)
            if not data:
                break
            if data == LDAP_HELLO:
                print('Received LDAP protocol anonymous bind request!')
            else:
                print('Received other data:\n', data)
            conn.close()
            print('Closed connection; listening again')
