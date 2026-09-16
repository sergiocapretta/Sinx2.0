
<html>
<!--======================================================================+
 File name   : index.php
 Begin       : 2010-08-04
 Last Update : 2016-01-16

 Description : The first page of login

 Author: Sergio Capretta

 (c) Copyright:
               Sergio Capretta
             
               ITALY
               www.sinx.it
               info@sinx.it

Sinx for Association - Gestionale per Associazioni no-profit
    Copyright (C) 2011 by Sergio Capretta

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
=========================================================================+-->
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="ISO-8859-1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Sinx</title>
  <link rel="stylesheet" >
  <style>
    body {
      font-family: Tahoma, Arial, Helvetica, sans-serif;
      background: linear-gradient(135deg, #778dff, #233592);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #333;
    }

 .login-container {
  width: 100%;
  max-width: 400px;
  margin: 5% auto;
  background: #ffffff;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 0 15px rgba(0,0,0,0.2);
  text-align: center;
}

/* Per schermi piccoli */
@media (max-width: 600px) {
  .login-container {
    width: 80%;
    padding: 1.5rem;
  }
  .login-container input {
    width: 100%;
    font-size: 1rem;
  }
}

    .login-header img {
      width: 120px;
      margin-bottom: 10px;
    }

    .login-header h2 {
      color: #233592;
      margin-bottom: 10px;
    }

    .login-form {
      text-align: left;
      margin-top: 1rem;
    }

    .login-form label {
      font-weight: bold;
      color: #333;
    }

    .login-form input[type="text"],
    .login-form input[type="password"] {
      width: 100%;
      padding: 10px;
      margin: 8px 0 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      background: #faffaa;
      font-size: 14px;
    }

    .login-form input[type="submit"] {
      width: 100%;
      padding: 10px;
      background: #233592;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }

    .login-form input[type="submit"]:hover {
      background: #4c5cff;
    }

    fieldset {
      border: 1px solid #ccc;
      border-radius: 8px;
      padding: 10px;
      margin-bottom: 15px;
      text-align: center;
    }

    legend {
      font-size: 12px;
      color: #555;
    }

    .login-footer {
      font-size: 12px;
      color: #666;
      margin-top: 1rem;
    }

    hr {
      margin: 1.5rem 0;
      border: none;
      height: 2px;
      background: #778dff;
    }

    .language-selector {
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 15px;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
  }

  .language-selector label {
    display: flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
  }

  .language-selector img {
    width: 30px;
    height: auto;
  }

  /* Nessun cambiamento su mobile */
  @media (max-width: 600px) {
    .language-selector {
      flex-direction: row; /* Forza orizzontale anche su mobile */
      gap: 10px;
    }
  }

  </style>
</head>
<body>

  <form action="./Conf_Login.php" method="post">
    <div class="login-container">
      <div class="login-header">
        <img src="./ImmTemplate/Nuovo_Logo_web.png" alt="Logo Sinx">
        <h2>Benvenuto in Sinx</h2>
        <small>Gestionale per Associazioni No-Profit</small>
      </div>

      <hr>

      <fieldset class="language-selector">
  <legend>Lingua</legend>
  <label><input type="radio" name="lang" value="./lang/ita/" checked><img src="./ImmTemplate/flag_ita.png" alt="Italiano"></label>
  <label><input type="radio" name="lang" value="./lang/eng/"><img src="./ImmTemplate/flag_eng.png" alt="English"></label>
</fieldset>

      <div class="login-form">
        <label for="usern">Username:</label>
        <input type="text" name="usern" id="usern" required>

        <label for="passwd">Password:</label>
        <input type="password" name="passwd" id="passwd" required>

        <input type="submit" value="Login">
      </div>

      <div class="login-footer">
        <small>Versione 2.0</small><br>

        <small>Sinx for Association © 2011–2025 by Sergio Capretta</small>
      </div>
    </div>
  </form>

</body>
</html>


