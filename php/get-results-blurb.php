<div >
    You probably want the most recent archive (zip file) (listed first).
    The other files are older snapshots. The filenames include the date and time of the snapshot.
    Within the zip file, there are many files. You probably want to focus on the file named 
    <span class="text-warning" style="font-weight: bold;">!AllDecisions.psv</span>.
    <br />
    <br />
    The files are *.psv - pipe-separated-values ("|"), rather than comma-separated-values (csv) files.
    <br />
    <br />
    You can easily import the *.psv files into google sheets: 
    <ol style="margin-top: 0px;">
        <li>First rename the file from *.psv to *.txt.</li>
        <li>Then use "File | Import".</li>
        <li>Specify the pipe character ("|") as the delimeter.</li>
    </ol>
    You can import *.psv files into Excel easily enough:
    <ul style="margin-top: 0px;">
        <li>"Data | GetData | File: Text/CSV" might be the fastest approach.</li>
        <li>"File | Open | All Files | (select *.psv file) | (follow the wizard)" is another approach.</li>
    </ul>
</div>
