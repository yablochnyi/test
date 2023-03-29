<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ChatsRelationManager extends RelationManager
{
    protected static string $relationship = 'chats';

    protected static ?string $recordTitleAttribute = 'user_id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('user_message')->formatStateUsing(function ($state, $record) {
                    $messages = $record->context;
                    return $messages[0]['content'];
                }),
                Textarea::make('assistant_message')->formatStateUsing(function ($state, $record) {
                    $messages = $record->context;
                    return $messages[1]['content'];
                }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id'),
                Tables\Columns\TextColumn::make('user_message')
                    ->formatStateUsing(function ($state, $record) {
                        $messages = $record->context;
                        $content = $messages[0]['content'];
                        return Str::limit($content, 40);
                    }),
                Tables\Columns\TextColumn::make('assistant_message')
                    ->formatStateUsing(function ($state, $record) {
                        $messages = $record->context;
                        $content = $messages[1]['content'];
                        return Str::limit($content, 40);
                    })
            ])
//            ->filters([
//                //
//            ])
//            ->headerActions([
//                Tables\Actions\CreateAction::make(),
//            ])
            ->actions([
                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ]);
//            ->bulkActions([
//                Tables\Actions\DeleteBulkAction::make(),
//            ]);
    }
}
