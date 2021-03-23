import os
import time
import pymysql
import pymysql.err
import pymysql.cursors

TIMEOUT = 30
start_time = time.time()

config = {
    "host": os.getenv("WORDPRESS_DB_HOST", ""),
    "user": os.getenv("WORDPRESS_DB_USER", ""),
    "password": os.getenv("WORDPRESS_DB_PASSWORD", ""),
    "dbname": os.getenv("WORDPRESS_DB_NAME", "")
}

def db_ready(host, user, password, dbname):
    while time.time() - start_time < TIMEOUT:
        try:
            connection = pymysql.connect(host=host,
                                         user=user,
                                         password=password,
                                         database=dbname,
                                         cursorclass=pymysql.cursors.DictCursor)

            print("DB is ready!")
            return True
        except pymysql.err.OperationalError:
            pass
        except Exception as e:
            print(type(e))
            print(str(e))
        finally:
            print("waiting for DB...")
            time.sleep(1)


# print(config)
db_ready(**config)