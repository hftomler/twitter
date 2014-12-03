#!/bin/sh

psql -U twitter twitter < bd.sql
psql -U twitter twitter < datos.sql
