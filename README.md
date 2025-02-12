<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# Sistema de Geração de Boletos - Banco Inter

Sistema desenvolvido em Laravel 11 para geração de boletos bancários integrado com a API do Banco Inter.

## Funcionalidades

- Autenticação de usuários com Laravel Breeze
- Geração de boletos bancários
- Integração com API do Banco Inter
- Dashboard com listagem de boletos
- Filtros por status e data
- Atualização automática de status dos boletos
- Interface moderna com Tailwind CSS

## Requisitos

- PHP 8.2 ou superior
- Composer
- Node.js e NPM
- Certificado digital do Banco Inter
- Credenciais de API do Banco Inter

## Instalação

1. Clone o repositório:
```bash
git clone [url-do-repositorio]
cd sistema-boletos
```

2. Instale as dependências do PHP:
```bash
composer install
```

3. Instale as dependências do Node.js:
```bash
npm install
```

4. Copie o arquivo de ambiente e configure as variáveis:
```bash
cp .env.example .env
```

5. Configure as seguintes variáveis no arquivo .env:
```
BANCO_INTER_URL=https://cdpj.partners.bancointer.com.br
BANCO_INTER_CLIENT_ID=seu-client-id
BANCO_INTER_CLIENT_SECRET=seu-client-secret
BANCO_INTER_CERTIFICATE_PATH=caminho/para/seu/certificado.crt
BANCO_INTER_CERTIFICATE_KEY=caminho/para/sua/chave.key
```

6. Gere a chave da aplicação:
```bash
php artisan key:generate
```

7. Execute as migrações do banco de dados:
```bash
php artisan migrate
```

8. Crie o link simbólico para o storage:
```bash
php artisan storage:link
```

9. Compile os assets:
```bash
npm run build
```

10. Inicie o servidor de desenvolvimento:
```bash
php artisan serve
```

## Configuração do Agendador de Tarefas

Para atualizar automaticamente o status dos boletos, adicione a seguinte entrada ao seu crontab:

```bash
* * * * * cd /caminho/para/seu/projeto && php artisan schedule:run >> /dev/null 2>&1
```

## Uso

1. Acesse o sistema através do navegador
2. Faça login ou registre uma nova conta
3. Na dashboard, você pode:
   - Visualizar todos os boletos gerados
   - Filtrar boletos por status e data
   - Gerar novos boletos
   - Visualizar detalhes dos boletos
   - Baixar boletos em PDF

## Segurança

- Mantenha suas credenciais do Banco Inter seguras
- Não compartilhe seu certificado digital
- Mantenha o sistema sempre atualizado
- Use HTTPS em produção

## Suporte

Em caso de dúvidas ou problemas, abra uma issue no repositório.
