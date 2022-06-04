months = dir('data');
months = {months.name}';
full_data = [];
for month = 1:length(months)
    if strcmp(months{month}, '.') || strcmp(months{month}, '..')
        continue;
    end
    
    full_data.(['month', months{month}]) = [];

    days = dir(['data/', months{month}]);
    days = {days.name}';
    for day = 1:length(days)
        if strcmp(days{day}, '03')
        end
        if strcmp(days{day}, '.') || strcmp(days{day}, '..')
            continue;
        end
        images = dir(['data/', months{month}, '/', days{day}]);
        images = {images.name}';
        images2save = [];
        for image = 1:length(images)
            if strcmp(images{image}, '.') || strcmp(images{image}, '..') || strcmp(images{image}, 'index.html')
                continue;
            end

            images2save{end+1} = ['data/', months{month}, '/', days{day}, '/', images{image}];
        end
        full_data.(['month', months{month}]).(['day', days{day}]) = images2save;
    end
end
fileID = fopen('full_data.txt', 'w');

fprintf(fileID, strrep(strrep(jsonencode(full_data), 'month', ''), 'day', ''));

fclose(fileID);


% writetable(struct2table(full_data), 'images.csv' );

